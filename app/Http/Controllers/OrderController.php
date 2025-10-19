<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
class OrderController extends Controller
{
    // Tampilkan semua order user
    public function index()
    {
        // Orders list page removed — redirect users to dashboard which already
        // contains order summaries.
        return redirect('/dashboard');
    }
    // Tampilkan detail order
    public function show($id)
    {
    $order = Order::with(['items.product'])->where('user_id', Auth::id())->findOrFail($id);

    if (request()->wantsJson() || request()->ajax()) {
        return response()->json($order);
    }

    return view('orders.show', compact('order'));
    }

    // Batalkan pesanan
    public function cancel($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->whereIn('status', ['belum_dibayar','disiapkan'])
            ->findOrFail($id);
        $order->status = 'dibatalkan';
        $order->save();
        return redirect('/orders')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    // Pengajuan pengembalian
    public function requestReturn($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->where('status', 'dikirim')
            ->findOrFail($id);
        $order->status = 'pengajuan_pengembalian';
        $order->save();
        return redirect('/orders/'.$id)->with('success', 'Pengajuan pengembalian dikirim.');
    }
}
