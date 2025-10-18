@extends('layouts.app')

@section('content')
    <h2>Daftar Pesanan</h2>
    <a href="/" style="display:inline-block; margin-bottom:18px; background:#e5e7eb; color:#222; border:none; padding:8px 20px; border-radius:6px; font-weight:500; text-decoration:none;">Back</a>
    @if(count($orders) > 0)
        <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th>ID</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->status }}</td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>
                            <a href="/orders/{{ $order->id }}" style="color:#2563eb;">Detail</a>
                            @if($order->status == 'belum_dibayar')
                                <form method="POST" action="/transactions/pay/{{ $order->id }}" style="display:inline; margin-left:8px;">
                                    @csrf
                                    <select name="payment_method" class="p-1 rounded border">
                                        <option value="transfer">Bank Transfer</option>
                                        <option value="cod">Cash on Delivery</option>
                                    </select>
                                    <button type="submit" style="background:#22c55e; color:white; border:none; padding:4px 10px; border-radius:4px; font-size:0.95em; margin-left:6px;">Bayar</button>
                                </form>
                                <form method="POST" action="/orders/cancel/{{ $order->id }}" style="display:inline; margin-left:4px;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" style="background:#ef4444; color:white; border:none; padding:4px 10px; border-radius:4px; font-size:0.95em;">Batal</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Belum ada pesanan.</p>
    @endif
@endsection
