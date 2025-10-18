@extends('layouts.app')

@section('content')
    <div style="max-width:600px; margin:auto; background:white; border-radius:12px; box-shadow:0 2px 12px #0002; padding:32px; display:grid; grid-template-columns:80px 1fr; gap:24px; align-items:center;">
        <div style="display:flex; justify-content:center; align-items:center;">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#e5e7eb" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M4 20c0-3.314 3.134-6 8-6s8 2.686 8 6"/></svg>
        </div>
        <div>
            <h2 style="margin-bottom:16px;">Profil Akun</h2>
            <a href="/" style="display:inline-block; margin-bottom:18px; background:#e5e7eb; color:#222; border:none; padding:8px 20px; border-radius:6px; font-weight:500; text-decoration:none;">Back</a>
            <div style="margin-bottom:8px;"><b>Nama:</b> {{ auth()->user()->name }}</div>
            <div style="margin-bottom:8px;"><b>Email:</b> {{ auth()->user()->email }}</div>
        </div>
        @if(auth()->user()->role === 'user')
            <div style="grid-column:1/3; margin-top:24px;">
                <hr>
                <h3>Riwayat Transaksi</h3>
                <ul>
                    @foreach(\App\Models\Transaction::whereHas('order', function($q){ $q->where('user_id', auth()->id()); })->get() as $trx)
                        <li>
                            Pesanan #{{ $trx->order->id }} - Status: {{ $trx->status }} - Total: Rp {{ number_format($trx->order->total, 0, ',', '.') }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(auth()->user()->role === 'admin')
            <div style="grid-column:1/3; margin-top:24px;">
                <hr>
                <h3>Statistik Penjualan</h3>
                <p>Total Produk: {{ \App\Models\Product::count() }}</p>
                <p>Total Transaksi: {{ \App\Models\Transaction::count() }}</p>
                <p>Total Penjualan: Rp {{ number_format(\App\Models\Order::where('status','paid')->sum('total'), 0, ',', '.') }}</p>
                <a href="/admin/dashboard" style="display:inline-block; margin-top:12px; background:#2563eb; color:white; border:none; padding:8px 20px; border-radius:6px; font-weight:500; text-align:center; text-decoration:none;">Kelola Produk & Transaksi</a>
            </div>
        @endif
    </div>
@endsection
