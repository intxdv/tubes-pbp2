<!-- Dashboard Admin Section (two-column layout) -->
<div id="dashboard-admin" class="content-section px-2 md:px-0">
    <h2 class="text-3xl font-bold mb-4">Admin Dashboard</h2>

    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-bottom:32px;">
        <div style="background:#f3f4f6; border-radius:8px; padding:18px; text-align:center;">
            <div style="font-size:2rem; font-weight:700; color:#2563eb;">{{ $products->count() }}</div>
            <div style="font-size:1rem; color:#555;">Total Produk</div>
        </div>
        <div style="background:#f3f4f6; border-radius:8px; padding:18px; text-align:center;">
            <div style="font-size:2rem; font-weight:700; color:#2563eb;">{{ $transactions->count() }}</div>
            <div style="font-size:1rem; color:#555;">Total Transaksi</div>
        </div>
        <div style="background:#f3f4f6; border-radius:8px; padding:18px; text-align:center;">
            <div style="font-size:2rem; font-weight:700; color:#2563eb;">
                Rp {{ number_format(\App\Models\Order::where('status','paid')->sum('total'), 0, ',', '.') }}
            </div>
            <div style="font-size:1rem; color:#555;">Total Penjualan</div>
        </div>
    </div>
    <div style="margin-bottom:16px; display:flex; gap:12px;">
        <a href="/products/create" style="background:#2563eb; color:white; padding:8px 18px; border-radius:6px; font-weight:500; text-decoration:none;">+ Tambah Produk</a>
        <a href="/admin/categories" style="background:#22c55e; color:white; padding:8px 18px; border-radius:6px; font-weight:500; text-decoration:none;">Kelola Kategori</a>
    </div>

    <h3>Produk</h3>
    <div style="display:grid; grid-template-columns:repeat(6, 1fr); gap:24px; margin-bottom:24px;">
        @foreach($products as $product)
            <div style="background:white; border-radius:12px; box-shadow:0 2px 8px #0002; padding:18px; display:flex; flex-direction:column; align-items:center; border:1px solid #f3f4f6;">
                <img src="{{ $product->image ? asset('images/' . rawurlencode($product->image)) : 'https://via.placeholder.com/120' }}" alt="{{ $product->name }}" style="width:100%; height:100px; object-fit:cover; border-radius:8px; margin-bottom:8px;">
                <div style="font-weight:600; color:#2563eb; margin-bottom:4px; text-align:center;">{{ $product->name }}</div>
                <div style="font-size:0.95rem; color:#222; margin-bottom:4px;">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                <div style="font-size:0.9rem; color:#555; margin-bottom:8px;">Stock: {{ $product->stock }}</div>
                <div style="display:flex; gap:8px;">
                    <a href="/products/{{ $product->id }}/edit" style="color:#2563eb; font-weight:500;">Edit</a>
                    <form method="POST" action="/products/{{ $product->id }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background:#ef4444; color:white; border:none; padding:4px 8px; border-radius:4px; font-size:13px;">Hapus</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    
    <h3>Transaksi</h3>
    <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
        <thead>
            <tr style="background:#f3f4f6;">
                <th>ID</th>
                <th>Status</th>
                <th>Metode</th>
                <th>Tanggal Bayar</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trx)
                <tr>
                    <td>{{ $trx->id }}</td>
                    <td>{{ $trx->status }}</td>
                    <td>{{ $trx->payment_method }}</td>
                    <td>{{ $trx->paid_at }}</td>
                    <td>Rp {{ number_format($trx->order->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
<!-- end dashboard-admin -->
