@extends('layouts.app')

@section('content')
    <div style="max-width:600px; margin:auto; border:1px solid #eee; border-radius:8px; padding:24px;">
       <img src="{{ $product->image ? asset('images/' . $product->image) : 'https://via.placeholder.com/200' }}" 
           alt="{{ $product->name }}" style="width:100%; height:250px; object-fit:cover; border-radius:8px; margin-bottom:16px;">
        <h2 style="margin:16px 0 8px 0;">{{ $product->name }}</h2>
        <p><b>Rp {{ number_format($product->price, 0, ',', '.') }}</b></p>
        <p>Stock: {{ $product->stock }}</p>
        <p style="font-size:15px; color:#555;">{{ $product->description }}</p>
        <div style="margin-top:16px; display:flex; gap:8px; align-items:center;">
            @auth
                <form method="POST" action="/cart/add/{{ $product->id }}" id="add-to-cart-form" style="display:flex; gap:8px; align-items:center;">
                    @csrf
                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" id="add-qty" style="width:60px; padding:4px; border-radius:4px; border:1px solid #ddd;">
                    <button type="submit" id="add-to-cart-btn" style="background:#2563eb; color:white; border:none; padding:8px 16px; border-radius:4px; font-weight:500; cursor:pointer;">Tambah ke Keranjang</button>
                </form>

                <form method="POST" action="/cart/buy-now/{{ $product->id }}">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" style="background:#f59e0b; color:white; border:none; padding:8px 16px; border-radius:4px; font-weight:700; cursor:pointer;">Beli Sekarang</button>
                </form>

                <div id="add-toast" style="display:none; margin-left:8px; background:#10b981; color:white; padding:8px 12px; border-radius:6px; font-weight:600;">Ditambahkan ke keranjang</div>

                <script>
                    (function(){
                        const form = document.getElementById('add-to-cart-form');
                        const btn = document.getElementById('add-to-cart-btn');
                        const qtyInput = document.getElementById('add-qty');
                        const toast = document.getElementById('add-toast');
                        function csrf(){ const m = document.querySelector('meta[name="csrf-token"]'); return m ? m.getAttribute('content') : ''; }

                        // If JS enabled, intercept submit and POST via fetch so page doesn't navigate
                        form.addEventListener('submit', function(e){
                            e.preventDefault();
                            const qty = qtyInput ? qtyInput.value : 1;
                            btn.disabled = true;
                            const url = form.getAttribute('action');
                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrf(),
                                    'Accept': 'text/html'
                                },
                                body: new URLSearchParams({ quantity: qty })
                            }).then(function(resp){
                                // treat any 2xx as success
                                if(resp.ok){
                                    // show toast briefly
                                    toast.style.display = 'inline-block';
                                    setTimeout(()=> toast.style.display='none', 1800);
                                    // update nav cart count if exists
                                    const badge = document.getElementById('nav-cart-count');
                                    if(badge){
                                        const current = parseInt(badge.textContent || '0') || 0;
                                        badge.textContent = current + 1;
                                        badge.style.display = 'inline-block';
                                    }
                                } else {
                                    // fallback: if server redirected or error, reload to maintain consistency
                                    window.location.reload();
                                }
                            }).catch(function(){
                                window.location.reload();
                            }).finally(function(){ btn.disabled = false; });
                        });
                    })();
                </script>
            @else
                {{-- guests: direct them to login; include return_to so they can come back --}}
                <a href="/login?return_to={{ urlencode(request()->fullUrl()) }}" style="display:inline-block; background:#2563eb; color:white; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:600;">Tambah ke Keranjang</a>
                <a href="/login?return_to={{ urlencode(request()->fullUrl()) }}" style="display:inline-block; background:#f59e0b; color:white; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:700;">Beli Sekarang</a>
            @endauth
        </div>
        <hr style="margin:24px 0;">
        <h4>Review Produk</h4>
        @php
            $avg = $product->reviews->avg('rating');
            $count = $product->reviews->count();
            $rounded = $avg ? (int) round($avg) : 0;
        @endphp
        @if($count === 0)
            <p style="font-size:14px; color:#555;">Belum ada review</p>
        @else
            <p style="font-size:14px; color:#555; display:flex; align-items:center; gap:8px;">
                <span>
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $rounded)
                            <span style="color:#f59e0b; font-size:16px;">★</span>
                        @else
                            <span style="color:#ddd; font-size:16px;">★</span>
                        @endif
                    @endfor
                </span>
                <span style="color:#555;">({{ $count }})</span>
            </p>
        @endif
        <div style="margin-top:16px;">
            @foreach($product->reviews as $review)
                <div style="border-bottom:1px solid #eee; padding:8px 0;">
                    <b>{{ $review->user->name }}</b> - Rating: {{ $review->rating }}<br>
                    <span style="font-size:13px; color:#555;">{{ $review->comment }}</span>
                    @auth
                        @if($review->user_id === auth()->id())
                            <form method="POST" action="/reviews/{{ $review->id }}" style="display:inline; margin-left:8px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background:#ef4444; color:white; border:none; padding:2px 8px; border-radius:4px; font-size:12px;">Hapus</button>
                            </form>
                        @endif
                    @endauth
                </div>
            @endforeach
        </div>
    </div>
@endsection
