@extends('layouts.app')

@section('content')
<style>
.content { max-width:1280px; margin: 0 auto; padding: 0; display:grid; grid-template-columns:200px 1fr; gap:24px; }
.sidebar { background:#fff; padding:20px; border-radius:14px; box-shadow:0 2px 8px rgba(0,0,0,0.05); position:sticky; top:120px; }
.content > section { padding-left:8px; }
.filter-title{ font-size:1.1em; font-weight:700; margin-bottom:12px; }
.price-input, .sort-select { width:100%; padding:10px; border:2px solid #e5e7eb; border-radius:8px; }
.apply-filter-btn{ width:100%; padding:12px; background:#253645; color:#fff; border-radius:10px; border:none; font-weight:700; }
.reset-filter-btn{ width:100%; padding:10px; border:2px solid #e5e7eb; border-radius:8px; background:transparent; text-align:center; }
.products-header{ display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
.products-grid{ display:grid; grid-template-columns:repeat(3, minmax(240px, 1fr)); gap:20px; margin-top:18px; align-items:start; }
.product-card{ background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 8px 30px rgba(16,24,40,0.06); cursor:pointer; display:flex; flex-direction:column; min-height:520px; }
.product-image{ width:300%; height:340px; object-fit:cover; object-position:center; background:#f3f4f6; display:block; }
.product-info{ padding:14px 16px; text-align:left; display:flex; flex-direction:column; gap:8px; flex:1 0 auto; }
.product-name{ font-weight:700; color:#1f2937; margin-bottom:0; font-size:1.05rem }
.product-price{ font-weight:800; color:#111827; margin-top:6px; font-size:1.05rem }
.rating-row{ display:flex; align-items:center; gap:8px; margin-top:6px; color:#6b7280; }
.stars{ color:#f6b93b; display:inline-block; }
.add-to-cart{ display:block; margin-top:12px; padding:12px 14px; background:#253645; color:#fff; border-radius:10px; text-align:center; text-decoration:none; font-weight:800; }
.no-results{ padding:60px; text-align:center; background:#fff; border-radius:8px; }
@media (max-width:1024px){ .content{ grid-template-columns:1fr; } .sidebar{ position:static; } .product-image{ height:300px; } }
</style>

<div style="max-width:1280px; margin:18px auto 0;">
    <!-- Local search input (keeps header unchanged) -->
    <form method="GET" action="{{ route('products.search') }}" style="max-width:1100px; margin:18px auto 0  ; display:flex; gap:12px; align-items:center;">
    <input type="text" name="q" id="localSearch" placeholder="Cari produk, kategori, atau brand..." value="{{ request('q', request('search')) }}" style="flex:1; padding:10px 14px; border-radius:8px; border:1px solid #e5e7eb;">
        <button type="submit" style="padding:10px 14px; background:#2563eb; color:white; border:none; border-radius:8px;">Cari</button>
    </form>
    <div class="content">
        <aside class="sidebar">
            <form id="filterForm" method="GET" action="{{ route('products.search') }}">
                <input type="hidden" name="q" value="{{ request('q', request('search')) }}">
                <div class="filter-section">
                    <div class="filter-title">Harga</div>
                    <input type="number" name="min_price" id="minPrice" class="price-input" placeholder="Harga minimum" value="{{ request('min_price') }}">
                    <div style="height:10px"></div>
                    <input type="number" name="max_price" id="maxPrice" class="price-input" placeholder="Harga maximum" value="{{ request('max_price') }}">
                </div>

                <div class="filter-section">
                    <div class="filter-title">Kategori</div>
                    @php
                        $dbCategories = \App\Models\Category::orderBy('name')->get();
                        $sel = request('categories', []);
                        if(is_string($sel) && $sel !== '') $selArr = explode(',', $sel); else $selArr = (array) $sel;
                        // include legacy single category param (from home form) so selection persists
                        if(request()->filled('category')) {
                            $selArr[] = (string) request('category');
                        }
                        // normalize to string ids for comparison
                        $selArr = array_map('strval', array_values($selArr));
                    @endphp
                    @foreach($dbCategories as $cat)
                        <div style="margin-bottom:8px;">
                            <label style="display:flex; gap:8px; align-items:center;">
                                <input type="checkbox" name="categories[]" value="{{ $cat->id }}" {{ in_array((string)$cat->id, $selArr) ? 'checked' : '' }}>
                                <span style="text-transform:capitalize;">{{ $cat->name }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div style="display:flex; gap:8px; margin-top:12px;">
                    <button type="submit" class="apply-filter-btn">Terapkan Filter</button>
                    <a href="{{ route('products.search') }}" class="reset-filter-btn">Reset</a>
                </div>
            </form>
        </aside>

        <section>
            <div class="products-header">
                <div>Menampilkan <strong id="productCount">{{ $products->total() }}</strong> produk</div>
                <div style="display:flex; gap:12px; align-items:center;">
                    <label style="color:#6b7280;">Urutkan:</label>
                    <select id="sortSelect" name="sort" form="filterForm" class="sort-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Paling Relevan</option>
                        <option value="price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                    </select>
                </div>
            </div>

            <div class="products-grid">
                @if($products->count() === 0)
                    <div class="no-results">🔍<h3>Tidak ada produk yang cocok</h3><p>Coba ubah kata kunci atau filter.</p></div>
                @endif

                @foreach($products as $product)
                    @php
                        // preview: override image for the first product so the layout can be inspected
                        $previewImage = asset('storage/images/ckvuLjHtxDC3NgpcSAotv5p33iXRGnnDv1SnCGhY.jpg');
                        $imgSrc = ($loop->first) ? $previewImage : ($product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/400');
                        $avg = $product->reviews()->exists() ? round($product->reviews()->avg('rating'),1) : null;
                        $rCount = $product->reviews()->count();
                    @endphp
                    <div class="product-card">
                        <a href="{{ url('/products/'.$product->id) }}" style="display:block;">
                            <img src="{{ $imgSrc }}" class="product-image" alt="{{ $product->name }}">
                        </a>
                        <div class="product-info">
                            <a href="{{ url('/products/'.$product->id) }}" style="text-decoration:none; color:inherit;"><div class="product-name">{{ $product->name }}</div></a>
                            <div class="product-price">Rp {{ number_format($product->price,0,',','.') }}</div>
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

                            <a href="{{ url('/products/'.$product->id) }}" class="add-to-cart">🛒 Tambah ke Keranjang</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top:18px;">{{ $products->links() }}</div>
        </section>
    </div>
</div>

<script>
// Keep header search input functioning: if user sets q in header it should submit to this search route
(function(){
    const headerSearch = document.getElementById('searchInput');
    if(headerSearch){
        headerSearch.addEventListener('keypress', function(e){ if(e.key === 'Enter'){ e.preventDefault(); const q = headerSearch.value; const params = new URLSearchParams(window.location.search); params.set('q', q); window.location = '{{ route('products.search') }}?'+params.toString(); } });
    }
})();
</script>

@endsection
