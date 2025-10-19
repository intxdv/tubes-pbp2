<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    // Menampilkan semua transaksi user
    public function index()
    {
        $transactions = Transaction::whereHas('order', function($q){
            $q->where('user_id', Auth::id());
        })->get();

        return view('transactions.index', compact('transactions'));
    }

    // Menampilkan detail transaksi
    public function show($id)
    {
        $transaction = Transaction::with(['order.items.product','order.user','order.address'])
            ->whereHas('order', function($q){
                $q->where('user_id', Auth::id());
            })->findOrFail($id);

        // If AJAX/JSON requested, return JSON payload
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($transaction);
        }

        return view('transactions.show', compact('transaction'));
    }

    // Proses pembayaran (user klik "Bayar")
    public function pay(Request $request, $orderId)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($orderId);


        // Create or get transaction record; default status is 'belum_dibayar'
        $transaction = Transaction::firstOrCreate(
            ['order_id' => $order->id],
            [
                // migration allows 'belum_dibayar' by default; we'll mark as 'disiapkan' when payment confirmed
                'payment_method' => $request->input('payment_method'),
            ]
        );

        // Treat the POST as confirming payment received (offline): mark transaction and order as 'disiapkan'
        $transaction->update([
            'status' => 'disiapkan',
            'payment_method' => $request->input('payment_method'),
            'paid_at' => now(),
        ]);

        // Update order status to prepared (ready to be shipped)
        $order->update(['status' => 'disiapkan']);

        // If AJAX request, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran dicatat.',
                'transaction' => $transaction
            ]);
        }

        // After payment, redirect user to the order detail page
        return redirect('/orders/' . $order->id)->with('success', 'Pembayaran dicatat.');
    }

    // Admin kirim pesanan
    public function ship($id)
    {
        $transaction = Transaction::findOrFail($id);
        $order = $transaction->order;
        // Admin verifies and marks the order as shipped only after verification
        if (Auth::user()->role === 'admin' && $order->status === 'disiapkan') {
            $order->update(['status' => 'dikirim']);
            $transaction->update(['status' => 'dikirim']);
        }

        return redirect('/transactions/'.$id);
    }

    // User konfirmasi pesanan diterima / ajukan pengembalian
    public function confirm(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $order = $transaction->order;

        if (Auth::user()->role === 'user' && $order->status === 'dikirim') {
            if ($request->input('sesuai') === 'tidak') {
                $order->update(['status' => 'pengembalian']);
                $transaction->update(['status' => 'pengembalian']);
            } else {
                $order->update(['status' => 'selesai']);
                $transaction->update(['status' => 'selesai']);
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan telah diperbarui',
                'newStatus' => $transaction->refresh()->status
            ]);
        }

        return redirect('/transactions/'.$id);
    }

    // User batalkan pesanan sebelum dikirim
    public function cancel($id)
    {
        $transaction = Transaction::findOrFail($id);
        $order = $transaction->order;

    if (Auth::user()->role === 'user' && $order->status === 'belum_dibayar') {
            $order->update(['status' => 'dibatalkan']);
            $transaction->update(['status' => 'dibatalkan']);
        }

        return redirect('/transactions/'.$id);
    }

    // Admin terima pengembalian
    public function returnAccept($id)
    {
        $transaction = Transaction::findOrFail($id);
        $order = $transaction->order;

        if (Auth::user()->role === 'admin' && $order->status === 'pengembalian') {
            $order->update(['status' => 'selesai']);
            $transaction->update(['status' => 'selesai']);
        }

        return redirect('/transactions/'.$id);
    }

    // Admin tolak pengembalian
    public function returnReject($id)
    {
        $transaction = Transaction::findOrFail($id);
        $order = $transaction->order;

        if (Auth::user()->role === 'admin' && $order->status === 'pengembalian') {
            $order->update(['status' => 'selesai']);
            $transaction->update(['status' => 'selesai']);
        }

        return redirect('/transactions/'.$id);
    }
}
