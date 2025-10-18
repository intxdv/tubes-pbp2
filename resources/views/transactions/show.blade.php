@extends('layouts.app')

@section('content')
    @php
        // Some controllers/views used $trx variable historically. Normalize to $transaction to avoid undefined variable errors.
        if (!isset($transaction) && isset($trx)) {
            $transaction = $trx;
        }
    @endphp
    <h2>Detail Transaksi #{{ $transaction->id }}</h2>
    <a href="/transactions" style="display:inline-block; margin-bottom:18px; background:#e5e7eb; color:#222; border:none; padding:8px 20px; border-radius:6px; font-weight:500; text-decoration:none;">Back</a>
    <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
        <tr><th style="width:180px; text-align:left;">Status</th><td>{{ $transaction->status }}</td></tr>
        <tr><th>Metode</th><td>{{ $transaction->payment_method }}</td></tr>
        <tr><th>Tanggal Bayar</th><td>{{ $transaction->paid_at }}</td></tr>
        <tr><th>Total</th><td>Rp {{ number_format($transaction->order->total, 0, ',', '.') }}</td></tr>
    </table>
    <h4>Detail Pesanan</h4>
    <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
        <thead>
            <tr style="background:#f3f4f6;">
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if($transaction->status == 'belum_dibayar')
        <form method="POST" action="/transactions/pay/{{ $transaction->order->id }}">
            @csrf
            <label>Pilih Metode Pembayaran:</label>
            <select name="payment_method">
                <option value="transfer">Transfer Bank</option>
                <option value="cod">COD</option>
            </select>
            <button type="submit" style="background:#22c55e; color:white; border:none; padding:8px 16px; border-radius:4px; margin-left:8px;">Bayar</button>
        </form>
        <form method="POST" action="/orders/cancel/{{ $transaction->order->id }}" style="display:inline; margin-left:8px;">
            @csrf
            @method('PATCH')
            <button type="submit" style="background:#ef4444; color:white; border:none; padding:8px 16px; border-radius:4px; margin-left:8px;">Batalkan</button>
        </form>
    @elseif($transaction->status == 'disiapkan' && auth()->user()->role === 'admin')
        <form method="POST" action="/transactions/ship/{{ $transaction->id }}">
            @csrf
            <button type="submit" style="background:#2563eb; color:white; border:none; padding:8px 16px; border-radius:4px; margin-left:8px;">Kirim Barang</button>
        </form>
    @elseif($transaction->status == 'disiapkan' && auth()->user()->role === 'user')
        <form method="POST" action="/orders/cancel/{{ $transaction->order->id }}" style="display:inline; margin-left:8px;">
            @csrf
            @method('PATCH')
            <button type="submit" style="background:#ef4444; color:white; border:none; padding:8px 16px; border-radius:4px; margin-left:8px;">Batalkan</button>
        </form>
    @elseif($transaction->status == 'dikirim' && auth()->user()->role === 'user')
        <form method="POST" action="/transactions/confirm/{{ $transaction->id }}">
            @csrf
            <label>Pesanan sesuai?</label>
            <select name="sesuai">
                <option value="ya">Ya</option>
                <option value="tidak">Tidak</option>
            </select>
            <button type="submit" style="background:#22c55e; color:white; border:none; padding:8px 16px; border-radius:4px; margin-left:8px;">Konfirmasi</button>
        </form>
    @elseif($transaction->status == 'pengajuan_pengembalian' && auth()->user()->role === 'admin')
        <form method="POST" action="/transactions/returnAccept/{{ $transaction->id }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:#22c55e; color:white; border:none; padding:8px 16px; border-radius:4px; margin-left:8px;">Terima Pengembalian</button>
        </form>
        <form method="POST" action="/transactions/returnReject/{{ $transaction->id }}" style="display:inline; margin-left:8px;">
            @csrf
            <button type="submit" style="background:#ef4444; color:white; border:none; padding:8px 16px; border-radius:4px; margin-left:8px;">Tolak Pengembalian</button>
        </form>
    @endif
@endsection
