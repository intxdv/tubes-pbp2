@extends('layouts.app')

@section('content')
    <style>
        .home-container{ max-width:1280px; margin:0 auto; padding:20px 12px 48px; }
        .home-banner-wrap{ display:flex; justify-content:center; margin-bottom:32px; }
        .home-banner{ background:linear-gradient(90deg,#2563eb 40%,#42afe2 100%); color:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,0.12); padding:32px 48px; font-size:1.5rem; font-weight:600; text-align:center; max-width:600px; }
        .home-search-form{ max-width:1100px; margin:0 auto 36px; display:flex; gap:16px; align-items:center; }
        .home-search-form input, .home-search-form select{ padding:10px 14px; border-radius:8px; border:1px solid #d1d5db; }
        .home-search-form input{ flex:1; }
        .home-search-form button{ background:#2563eb; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-weight:600; cursor:pointer; }
        .products-grid{ display:grid; grid-template-columns:repeat(3, minmax(240px, 1fr)); gap:20px; align-items:start; }
        .product-card{ background:#fff; border-radius:26px; box-shadow:0 20px 48px rgba(15,23,42,0.14); cursor:pointer; display:flex; flex-direction:column; gap:14px; overflow:visible; padding:20px; transition:transform .18s ease, box-shadow .18s ease; text-decoration:none; color:inherit; }
        .product-card:hover{ transform:translateY(-8px); box-shadow:0 26px 60px rgba(15,23,42,0.18); }
        .product-thumb{ display:flex; align-items:center; justify-content:center; width:100%; aspect-ratio:1 / 1; border-radius:24px; background:#f3f4f6; overflow:visible; position:relative; }
        .product-thumb img{ width:100%; height:100%; object-fit:cover; border-radius:inherit; transition:transform .28s ease; box-shadow:0 18px 36px rgba(15,23,42,0.1); }
        .product-card:hover .product-thumb img{ transform:scale(1.05); }
        .product-info{ padding:0 4px 6px; text-align:left; display:flex; flex-direction:column; gap:10px; flex:1 0 auto; }
        .product-name{ font-weight:700; color:#1f2937; margin-bottom:0; font-size:1.05rem; }
        .product-price{ font-weight:800; color:#111827; margin-top:6px; font-size:1.05rem; }
        .rating-row{ display:flex; align-items:center; gap:8px; margin-top:6px; color:#6b7280; }
        .stars{ color:#f6b93b; display:inline-block; }
        @media (max-width:1024px){
            .home-search-form{ flex-direction:column; align-items:stretch; }
            .home-search-form button{ width:100%; }
            .products-grid{ grid-template-columns:repeat(2, minmax(220px, 1fr)); }
        }
        @media (max-width:640px){
            .products-grid{ grid-template-columns:1fr; }
        }
    </style>

    <div class="home-container">
        <div class="home-banner-wrap">
            <div class="home-banner">
                <span style="font-size:2rem;">🎉</span> Promo September! Diskon hingga <b>30%</b> untuk produk terpilih!
            </div>
        </div>

        <form method="GET" action="/search" class="home-search-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk...">
            <select name="category">
                <option value="">Semua Kategori</option>
                @foreach(App\Models\Category::all() as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <button type="submit">Cari</button>
        </form>

        @php
            $query = App\Models\Product::query();
            if(request('search')) $query->where('name', 'like', '%'.request('search').'%');
            if(request('category')) $query->where('category_id', request('category'));
            $products = $query->get();
        @endphp

        <div class="products-grid">
            @foreach($products as $product)
                @php
                    $imgSrc = $product->image
                        ? asset('images/' . rawurlencode($product->image))
                        : 'https://images.unsplash.com/photo-1512499617640-c2f999098c01?auto=format&fit=crop&w=1000&q=80';
                    $avg = $product->reviews()->exists() ? round($product->reviews()->avg('rating'),1) : null;
                    $rCount = $product->reviews()->count();
                @endphp
                <div class="product-card" onclick="window.location='/products/{{ $product->id }}'">
                    <a href="/products/{{ $product->id }}" class="product-thumb">
                        <img src="{{ $imgSrc }}" alt="{{ $product->name }}">
                    </a>
                    <div class="product-info">
                        <a href="/products/{{ $product->id }}" style="text-decoration:none; color:inherit;"><div class="product-name">{{ $product->name }}</div></a>
                        <div class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        <div style="color:#6b7280; font-size:0.9rem;">Stok: {{ $product->stock ?? '-' }}</div>

                        <div class="rating-row">
                            <div class="stars">
                                @php $filled = $avg ? floor($avg) : 0; @endphp
                                @for($i=1;$i<=5;$i++)
                                    @if($i <= $filled)
                                        <span style="font-size:1.05rem;">★</span>
                                    @else
                                        <span style="font-size:1.05rem; color:#e6e7ea;">★</span>
                                    @endif
                                @endfor
                            </div>
                            <div style="font-size:0.95rem; color:#374151;">{{ $avg ? $avg : '—' }} @if($rCount) <span style="color:#6b7280;">({{ $rCount }})</span> @endif</div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
