<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function loginPage()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
            // If not admin, logout and redirect back
            Auth::logout();
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or insufficient permissions.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
    // Dashboard admin
    public function dashboard()
    {
        try {
            $admin = Auth::user();
            
            // Get buyers count with query optimization
            try {
                $totalBuyers = DB::table('users')
                    ->where('role', 'user')
                    ->select(DB::raw('COUNT(*) as count'))
                    ->limit(1000)
                    ->value('count');
            } catch (\Exception $e) {
                $totalBuyers = 0;
                Log::error('Error counting buyers: ' . $e->getMessage());
            }
            
            // Order summary with query optimization
            try {
                $orderStats = DB::table('orders')
                    ->select(
                        DB::raw('COUNT(*) as total'),
                        DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
                        DB::raw('SUM(CASE WHEN status IN ("pending", "processing") THEN 1 ELSE 0 END) as pending')
                    )
                    ->where('status', '!=', 'cart')
                    ->limit(1000)
                    ->first();
                
                $totalOrders = $orderStats->total ?? 0;
                $completedOrders = $orderStats->completed ?? 0;
                $pendingOrders = $orderStats->pending ?? 0;
            } catch (\Exception $e) {
                $totalOrders = $completedOrders = $pendingOrders = 0;
                Log::error('Error counting orders: ' . $e->getMessage());
            }
            
            // Revenue summary with query optimization
            try {
                // Monthly revenue data for chart
                $monthlyRevenue = DB::table('transactions')
                    ->join('orders', 'transactions.order_id', '=', 'orders.id')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->selectRaw("strftime('%m', transactions.created_at) as month, SUM(order_items.quantity * order_items.price) as total")
                    ->whereRaw("strftime('%Y', transactions.created_at) = ?", [Carbon::now()->year])
                    ->where('transactions.status', 'selesai')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                
                // Calculate optimized revenue summaries
                $revenueStats = DB::table('transactions')
                    ->join('orders', 'transactions.order_id', '=', 'orders.id')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->select(
                        DB::raw('SUM(CASE WHEN transactions.created_at BETWEEN ? AND ? THEN order_items.quantity * order_items.price ELSE 0 END) as weekly'),
                        DB::raw('SUM(CASE WHEN transactions.created_at BETWEEN ? AND ? THEN order_items.quantity * order_items.price ELSE 0 END) as monthly'),
                        DB::raw('SUM(CASE WHEN transactions.created_at BETWEEN ? AND ? THEN order_items.quantity * order_items.price ELSE 0 END) as yearly')
                    )
                    ->where('transactions.status', 'selesai')
                    ->setBindings([
                        Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek(),
                        Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(),
                        Carbon::now()->startOfYear(), Carbon::now()->endOfYear()
                    ])
                    ->first();
                
                $weekly = $revenueStats->weekly ?? 0;
                $monthly = $revenueStats->monthly ?? 0;
                $yearly = $revenueStats->yearly ?? 0;
            } catch (\Exception $e) {
                $weekly = $monthly = $yearly = 0;
                $monthlyRevenue = collect();
                Log::error('Error calculating revenue: ' . $e->getMessage());
            }

            // Latest transactions for quick overview
            try {
                $latestTransactions = Transaction::with(['order.user'])
                    ->select('id', 'order_id', 'amount', 'status', 'created_at')
                    ->latest()
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
                $latestTransactions = collect();
                Log::error('Error fetching latest transactions: ' . $e->getMessage());
            }

            return view('admin.dashboard', compact(
                'admin',
                'totalBuyers',
                'totalOrders',
                'completedOrders',
                'pendingOrders',
                'weekly',
                'monthly',
                'yearly',
                'monthlyRevenue',
                'latestTransactions'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat memuat dashboard. Silakan coba lagi.');
        }
    }

    // Products management
    public function products()
    {
        try {
            $products = Product::with(['category' => function($query) {
                $query->withCount('products');
            }])->get();
            $categories = Category::withCount('products')->get();
            return view('admin.products', compact('products', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error loading products page: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat memuat halaman produk. Silakan coba lagi.');
        }
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->only(['name','price','stock','category_id']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        // If AJAX (expects JSON), return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'product' => $product
            ]);
        }

        return redirect()->route('admin.products')->with('success', 'Produk berhasil ditambahkan');
    }

    public function getProduct($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('image');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        return redirect()->route('admin.products')->with('success', 'Produk berhasil diupdate');
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Produk berhasil dihapus');
    }

    // Categories management
    public function categories()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories', compact('categories'));
    }

    public function addCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100|unique:categories']);
        Category::create(['name' => $request->name]);
        return redirect()->route('admin.products')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.products')->with('error', 'Tidak dapat menghapus kategori yang masih memiliki produk');
        }
        $category->delete();
        return redirect()->route('admin.products')->with('success', 'Kategori berhasil dihapus');
    }

    // Transactions management
    public function transactions()
    {
        try {
            $transactions = Transaction::with([
                'order.user',
                'order.items.product',
                'order.address'
            ])->latest()->get();
            
            return view('admin.transactions', compact('transactions'));
        } catch (\Exception $e) {
            Log::error('Error loading transactions page: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat memuat halaman transaksi. Silakan coba lagi.');
        }
    }

    public function getTransaction($id)
    {
        $transaction = Transaction::with([
            'order.user', 
            'order.items.product', 
            'order.address'
        ])->findOrFail($id);
        
        return response()->json($transaction);
    }

    public function updateTransactionStatus(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $currentStatus = $transaction->status;
        
        // Validasi status yang bisa diubah oleh admin
        if ($currentStatus === 'disiapkan') {
            $transaction->status = 'dikirim';
            $transaction->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diubah menjadi Dikirim',
                'newStatus' => 'dikirim'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Status tidak dapat diubah'
        ], 400);
    }

    // Statistics
    public function statistics()
    {
        return view('admin.statistics');
    }

    // Statistics API
    public function getStatisticsSummary()
    {
        $totalRevenue = DB::table('transactions')
            ->join('orders', 'transactions.order_id', '=', 'orders.id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('transactions.status', 'selesai')
            ->sum(DB::raw('order_items.quantity * order_items.price'));
            
        $totalQuantity = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('transactions', 'transactions.order_id', '=', 'orders.id')
            ->where('transactions.status', 'selesai')
            ->sum('order_items.quantity');
            
        $successRate = round(
            (Order::where('status', 'completed')->count() / 
            Order::where('status', '!=', 'cart')->count()) * 100, 
            2
        );
        
        $averageRating = round(Review::avg('rating'), 2);

        return response()->json([
            'totalRevenue' => $totalRevenue,
            'totalQuantity' => $totalQuantity,
            'successRate' => $successRate,
            'averageRating' => $averageRating
        ]);
    }

    public function getRevenueChart()
    {
        $revenue = DB::table('transactions')
            ->join('orders', 'transactions.order_id', '=', 'orders.id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('transactions.status', 'selesai')
            ->whereBetween('transactions.created_at', [Carbon::now()->subDays(30), Carbon::now()])
            ->selectRaw("strftime('%Y-%m-%d', transactions.created_at) as date, SUM(order_items.quantity * order_items.price) as total")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'labels' => $revenue->pluck('date'),
            'data' => $revenue->pluck('total')
        ]);
    }

    public function getCategoryChart()
    {
        $categories = Category::withSum(['products.orderItems' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->where('status', 'completed');
                });
            }], 'order_items.total_price')
            ->having('products_order_items_sum_total_price', '>', 0)
            ->get();

        $total = $categories->sum('products_order_items_sum_total_price');
        
        return response()->json([
            'labels' => $categories->pluck('name'),
            'data' => $categories->pluck('products_order_items_sum_total_price'),
            'percentages' => $categories->map(function($cat) use ($total) {
                return round(($cat->products_order_items_sum_total_price / $total) * 100, 2);
            })
        ]);
    }
}
