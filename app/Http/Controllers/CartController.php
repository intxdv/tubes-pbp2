<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/*
 * Restore DB-backed cart behavior: prefer saved Order for authenticated users
 * but fall back to session-based cart for guests. This mirrors the previous
 * behavior before the simplified session-only implementation.
 */

class CartController extends Controller
{
    // Show a simple session-backed cart
    public function index()
    {
        // Use session-backed cart for both guests and authenticated users.
        // We intentionally do not create a DB 'cart' order until final checkout.
        $items = session('cart.items', []);

        // clear buy_now session when viewing the cart (so it doesn't persist)
        if (session()->has('buy_now')) {
            session()->forget('buy_now');
        }

        return view('cart.index', compact('items'));
    }

    // Add a product to the session cart
    public function add(Request $request, $productId)
    {
        $product = Product::find($productId);
        if (! $product) {
            return redirect()->back()->with('error', 'Product not found');
        }

        $qty = max(1, intval($request->input('quantity', 1)));

        // Session-backed cart (used for both guests and logged-in users).
        $cart = session('cart.items', []);
        $found = false;
        foreach ($cart as &$it) {
            if ($it['product_id'] == $product->id) {
                $it['quantity'] += $qty;
                $found = true;
                break;
            }
        }
        unset($it);
        if (! $found) {
            $cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $qty,
            ];
        }
        session(['cart.items' => $cart]);
        return redirect('/cart');
    }

    // Remove a product from the session cart by product id
    public function remove($productId)
    {
        // operate on session-backed cart only
        $cart = session('cart.items', []);
        $cart = array_values(array_filter($cart, function($it) use ($productId) {
            return $it['product_id'] != $productId;
        }));
        session(['cart.items' => $cart]);
        return redirect('/cart');
    }

    // Buy now: store a single-item payload in session and go to checkout
    public function buyNow(Request $request, $productId)
    {
        $product = Product::find($productId);
        if (! $product) {
            return redirect()->back()->with('error', 'Product not found');
        }
        $qty = max(1, intval($request->input('quantity', 1)));
        $payload = [
            'items' => [
                [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $qty,
                ]
            ]
        ];
        session(['buy_now' => $payload]);
        return redirect('/cart/checkout?buy_now=1');
    }

    // Show a simple checkout page using the session payload
    public function showCheckout(Request $request)
    {
        $items = [];

        if ($request->query('buy_now')) {
            $payload = session('buy_now', null);
            if ($payload && isset($payload['items']) && is_array($payload['items'])) {
                foreach ($payload['items'] as $it) {
                    // normalize into an object with a product property like OrderItem
                    $items[] = (object) [
                        'id' => isset($it['product_id']) ? 'p'.$it['product_id'] : null,
                        'product' => (object) [
                            'id' => $it['product_id'] ?? null,
                            'name' => $it['name'] ?? ($it['title'] ?? 'Produk'),
                        ],
                        'price' => $it['price'] ?? 0,
                        'quantity' => $it['quantity'] ?? 1,
                    ];
                }
            }
        } else {
            // try DB-backed cart for authenticated users
            if (Auth::check()) {
                $order = Order::where('user_id', Auth::id())->where('status', 'cart')->first();
                $items = $order ? $order->items : [];
            } else {
                // session-backed cart for guests; normalize array entries
                $sessionItems = session('cart.items', []);
                if (is_array($sessionItems)) {
                    foreach ($sessionItems as $it) {
                        $items[] = (object) [
                            'id' => $it['product_id'] ?? null,
                            'product' => (object) [
                                'id' => $it['product_id'] ?? null,
                                'name' => $it['name'] ?? ($it['title'] ?? 'Produk'),
                            ],
                            'price' => $it['price'] ?? 0,
                            'quantity' => $it['quantity'] ?? 1,
                        ];
                    }
                } else {
                    $items = $sessionItems;
                }
            }
        }

        return view('cart.checkout', compact('items'));
    }

    // Finalize checkout: for simplicity this clears the session cart and redirects
    public function checkout(Request $request)
    {
        // If the cart page posts with a preview flag, store the selected items as
        // a buy_now payload and redirect to the GET checkout page which will
        // render the address / payment method form and order summary.
        if ($request->has('preview')) {
            $items = $request->input('items', []);
            $payloadItems = [];
            if (is_array($items)) {
                foreach ($items as $it) {
                    // Only include items that were selected on the cart page
                    if (! isset($it['selected'])) continue;
                    $qty = isset($it['quantity']) ? intval($it['quantity']) : 1;
                    $payloadItems[] = [
                        'product_id' => $it['product_id'] ?? ($it['id'] ?? null),
                        'name' => $it['name'] ?? ($it['title'] ?? 'Item'),
                        'price' => isset($it['price']) ? floatval($it['price']) : 0,
                        'quantity' => $qty,
                    ];
                }
            }

            if (count($payloadItems) === 0) {
                return redirect('/cart')->with('error', 'Tidak ada produk terpilih untuk checkout');
            }

            session(['buy_now' => ['items' => $payloadItems]]);
            return redirect('/cart/checkout?buy_now=1');
        }

        // Finalize checkout: create Order + OrderItems for authenticated users.
        $payloadWrapper = session('buy_now', []);
        $payload = is_array($payloadWrapper) ? ($payloadWrapper['items'] ?? []) : [];
        if (empty($payload) || ! is_array($payload)) {
            return redirect('/cart')->with('error', 'Tidak ada produk untuk diproses.');
        }

        if (! Auth::check()) {
            // require login for order creation (orders table requires a user_id)
            return redirect('/login')->with('warning', 'Silakan login dulu sebelum checkout');
        }

        $userId = Auth::id();

        $validated = $request->validate([
            'payment_method' => ['required', 'in:transfer,cod'],
            'address_id' => ['nullable', 'integer'],
            'recipient_name' => ['nullable', 'string', 'required_without:address_id'],
            'phone' => ['nullable', 'string', 'required_without:address_id'],
            'address' => ['nullable', 'string', 'required_without:address_id'],
        ], [
            'payment_method.required' => 'Pilih metode pembayaran lebih dulu.',
            'recipient_name.required_without' => 'Nama penerima wajib diisi bila memakai alamat baru.',
            'phone.required_without' => 'Nomor telepon wajib diisi bila memakai alamat baru.',
            'address.required_without' => 'Alamat lengkap wajib diisi bila memakai alamat baru.',
        ]);

        $existingAddress = null;
        $newAddressData = null;
        if (! empty($validated['address_id'])) {
            $existingAddress = Address::where('user_id', $userId)
                ->where('id', intval($validated['address_id']))
                ->first();

            if (! $existingAddress) {
                return redirect()->back()->withErrors(['address_id' => 'Alamat tidak ditemukan.'])->withInput();
            }
        } else {
            $newAddressData = [
                'user_id' => $userId,
                'recipient_name' => trim($validated['recipient_name'] ?? ''),
                'phone' => trim($validated['phone'] ?? ''),
                'address' => trim($validated['address'] ?? ''),
            ];
        }

        $total = 0;
        foreach ($payload as $it) {
            $price = isset($it['price']) ? floatval($it['price']) : 0;
            $qty = isset($it['quantity']) ? intval($it['quantity']) : 1;
            $total += $price * $qty;
        }

        DB::transaction(function () use ($payload, $total, $userId, $existingAddress, $newAddressData, $validated) {
            $addressId = $existingAddress?->id;
            if (! $addressId && is_array($newAddressData)) {
                $addressId = Address::create($newAddressData)->id;
            }

            $order = Order::create([
                'user_id' => $userId,
                'address_id' => $addressId,
                'total' => $total,
                'status' => 'belum_dibayar',
            ]);

            foreach ($payload as $it) {
                $pid = $it['product_id'] ?? null;
                $qty = isset($it['quantity']) ? intval($it['quantity']) : 1;
                $price = isset($it['price']) ? floatval($it['price']) : 0;
                if (! $pid) {
                    continue;
                }

                $product = Product::find($pid);
                if (! $product) {
                    continue;
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => max(1, $qty),
                    'price' => $price,
                ]);
            }

            Transaction::create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'status' => 'belum_dibayar',
            ]);
        });

        // remove purchased products from cart session but keep remaining items intact
        $cartItems = session('cart.items');
        if (is_array($cartItems) && count($cartItems) > 0) {
            $purchasedIds = [];
            foreach ($payload as $it) {
                if (isset($it['product_id'])) {
                    $purchasedIds[] = intval($it['product_id']);
                }
            }

            if (! empty($purchasedIds)) {
                $remaining = array_values(array_filter($cartItems, function ($item) use ($purchasedIds) {
                    $id = isset($item['product_id']) ? intval($item['product_id']) : null;
                    return $id === null || ! in_array($id, $purchasedIds, true);
                }));
                session(['cart.items' => $remaining]);
            }
        }

        session()->forget('buy_now');

        return redirect('/orders')->with('success', 'Pesanan dibuat. Silakan lanjutkan pembayaran.');
    }
}
