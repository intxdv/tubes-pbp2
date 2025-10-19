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
            return redirect('/login');
        }
        if ($user->role === 'admin') {
            return redirect('/products/' . $productId)->with('error', 'Admin tidak bisa mereview produk.');
        }

        // Check that the user has at least one order with status 'selesai' containing this product
    $hasCompleted = Order::where('user_id', $user->id)
            ->where('status', 'selesai')
            ->whereExists(function($query) use ($productId) {
        $query->select(DB::raw(1))
                      ->from('order_items')
                      ->whereColumn('order_items.order_id', 'orders.id')
                      ->where('order_items.product_id', $productId);
            })->exists();

        if (! $hasCompleted) {
            return redirect('/products/' . $productId)->with('error', 'Anda hanya dapat memberi review jika Anda sudah menyelesaikan pesanan yang berisi produk ini.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required',
        ]);

        Review::create([
            'user_id' => $user->id,
            'product_id' => $productId,
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
