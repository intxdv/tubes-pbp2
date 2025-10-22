<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
class ReviewController extends Controller
{
    // Simpan review
    public function store(Request $request, $productId)
    {
        $user = Auth::user();
        if (!$user) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
            }
            return redirect('/login');
        }
        
        if ($user->role === 'admin') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Admin tidak bisa mereview produk.'], 403);
            }
            return redirect('/products/' . $productId)->with('error', 'Admin tidak bisa mereview produk.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'order_id' => 'nullable|integer|exists:orders,id',
        ]);

        $orderId = $validated['order_id'] ?? null;

        // If order_id provided, check if user already reviewed this product for THIS specific order
        if ($orderId) {
            $alreadyReviewed = Review::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->where('order_id', $orderId)
                ->exists();

            if ($alreadyReviewed) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Anda sudah memberi review untuk produk ini di pesanan ini.'], 400);
                }
                return redirect('/products/' . $productId)->with('error', 'Anda sudah memberi review untuk produk ini di pesanan ini.');
            }

            // Validate that this order belongs to user, is 'selesai', and contains this product
            $validOrder = Order::where('id', $orderId)
                ->where('user_id', $user->id)
                ->where('status', 'selesai')
                ->whereHas('items', function($q) use ($productId) {
                    $q->where('product_id', $productId);
                })->exists();

            if (!$validOrder) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Pesanan tidak valid untuk review.'], 403);
                }
                return redirect('/products/' . $productId)->with('error', 'Pesanan tidak valid untuk review.');
            }
        } else {
            // Fallback: check that user has at least one completed order with this product
            $hasCompleted = Order::where('user_id', $user->id)
                ->where('status', 'selesai')
                ->whereExists(function($query) use ($productId) {
                    $query->select(DB::raw(1))
                          ->from('order_items')
                          ->whereColumn('order_items.order_id', 'orders.id')
                          ->where('order_items.product_id', $productId);
                })->exists();

            if (!$hasCompleted) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Anda hanya dapat memberi review jika sudah menyelesaikan pesanan yang berisi produk ini.'], 403);
                }
                return redirect('/products/' . $productId)->with('error', 'Anda hanya dapat memberi review jika sudah menyelesaikan pesanan yang berisi produk ini.');
            }
        }

        Review::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'order_id' => $orderId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas review Anda.'
            ]);
        }

        return redirect('/products/' . $productId)->with('success', 'Terima kasih atas review Anda.');
    }

    // Hapus review
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }
        $review->delete();
        return back()->with('success', 'Review berhasil dihapus');
    }
}
