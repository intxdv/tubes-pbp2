@extends('layouts.app')

@section('content')
    <h2>Detail Pesanan #{{ $order->id }}</h2>
    <p>Status: <b>{{ $order->status }}</b></p>
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
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p>Total Pesanan: <b>Rp {{ number_format($order->total, 0, ',', '.') }}</b></p>
    @if($order->status == 'belum_dibayar')
        <form method="POST" action="/transactions/pay/{{ $order->id }}">
            @csrf
            <label>Pilih Metode Pembayaran:</label>
            <select name="payment_method">
                <option value="transfer">Transfer Bank</option>
                <option value="cod">COD</option>
            </select>
            <button type="submit" style="background:#22c55e; color:white; border:none; padding:8px 16px; border-radius:4px; margin-left:8px;">Bayar</button>
        </form>
        <form method="POST" action="/orders/cancel/{{ $order->id }}" style="display:inline; margin-left:8px;">
            @csrf
            @method('PATCH')
            <button type="submit" style="background:#ef4444; color:white; border:none; padding:8px 16px; border-radius:4px; margin-left:8px;">Batalkan</button>
        </form>
    @elseif($order->status == 'disiapkan')
        <form method="POST" action="/orders/cancel/{{ $order->id }}" style="display:inline; margin-left:8px;">
            @csrf
            @method('PATCH')
            <button type="submit" style="background:#ef4444; color:white; border:none; padding:8px 16px; border-radius:4px; margin-left:8px;">Batalkan</button>
        </form>
    @elseif($order->status == 'dikirim' && $order->transaction)
        <form method="POST" action="/transactions/confirm/{{ $order->transaction->id }}">
            @csrf
            <label>Pesanan sesuai?</label>
            <select name="sesuai">
                <option value="ya">Ya</option>
                <option value="tidak">Tidak</option>
            </select>
            <button type="submit" style="background:#22c55e; color:white; border:none; padding:8px 16px; border-radius:4px; margin-left:8px;">Konfirmasi</button>
        </form>
    @endif
@endsection
