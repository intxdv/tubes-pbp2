@extends('layouts.app')

@section('content')
    <div style="max-width:600px; margin:auto; border:1px solid #eee; border-radius:8px; padding:24px;">
       <img src="{{ $product->image ? asset('images/' . rawurlencode($product->image)) : 'https://via.placeholder.com/200' }}" 
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
        <h4 style="font-size:20px; font-weight:600; margin-bottom:16px;">Review Produk</h4>
        @php
            $reviews = $product->reviews;
            $count = $reviews->count();
            $avg = $count > 0 ? $reviews->avg('rating') : 0;
            $rounded = $avg ? (int) round($avg) : 0;
        @endphp
        
        @if($count === 0)
            <p style="font-size:14px; color:#888; margin-bottom:16px;">Belum ada review untuk produk ini</p>
        @else
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px; padding:12px; background:#f9fafb; border-radius:8px;">
                <div style="display:flex; align-items:center; gap:4px;">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $rounded)
                            <span style="color:#f59e0b; font-size:20px;">★</span>
                        @else
                            <span style="color:#ddd; font-size:20px;">★</span>
                        @endif
                    @endfor
                </div>
                <span style="font-size:18px; font-weight:600; color:#1f2937;">{{ number_format($avg, 1) }}</span>
                <span style="font-size:14px; color:#6b7280;">({{ $count }} review{{ $count > 1 ? 's' : '' }})</span>
            </div>
        @endif
        
        <div style="margin-top:16px; max-height:500px; overflow-y:auto;">
            @foreach($reviews as $review)
                <div style="border-bottom:1px solid #e5e7eb; padding:16px 0;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                        <div>
                            <b style="color:#1f2937;">{{ $review->user->name }}</b>
                            <div style="display:flex; gap:2px; margin-top:4px;">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <span style="color:#f59e0b; font-size:14px;">★</span>
                                    @else
                                        <span style="color:#ddd; font-size:14px;">★</span>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        @auth
                            @if($review->user_id === auth()->id())
                                <form method="POST" action="/reviews/{{ $review->id }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus review ini?')" style="background:#ef4444; color:white; border:none; padding:6px 12px; border-radius:4px; font-size:12px; cursor:pointer;">Hapus</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                    <p style="font-size:14px; color:#374151; line-height:1.6;">{{ $review->comment }}</p>
                    <p style="font-size:12px; color:#9ca3af; margin-top:6px;">{{ $review->created_at->diffForHumans() }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
