@extends('layouts.app')

@section('content')
    <div style="width:100%; display:flex; justify-content:center; margin-bottom:32px;">
        <div style="background:linear-gradient(90deg,#2563eb 40%,#42afe2 100%); color:white; border-radius:16px; box-shadow:0 2px 12px #0002; padding:32px 48px; font-size:1.5rem; font-weight:600; text-align:center; max-width:600px;">
            <span style="font-size:2rem;">🎉</span> Promo September! Diskon hingga <b>30%</b> untuk produk terpilih!
        </div>
    </div>

    <form method="GET" action="/search" style="max-width:1100px; margin:auto; display:flex; gap:16px; align-items:center; margin-bottom:32px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." style="flex:1; padding:8px 12px; border-radius:6px; border:1px solid #ddd;">
        <select name="category" style="padding:8px 12px; border-radius:6px; border:1px solid #ddd;">
            <option value="">Semua Kategori</option>
            @foreach(App\Models\Category::all() as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" style="background:#2563eb; color:white; border:none; padding:8px 20px; border-radius:6px; font-weight:500; cursor:pointer;">Cari</button>
    </form>

    <div style="max-width:1100px; margin:auto; display:grid; grid-template-columns:repeat(3, 1fr); gap:20px; margin-top:32px; align-items:start;"> 
        @php
            $query = App\Models\Product::query();
            if(request('search')) $query->where('name', 'like', '%'.request('search').'%');
            if(request('category')) $query->where('category_id', request('category'));
            $products = $query->get();
        @endphp
        @foreach($products as $product)
            <a href="/products/{{ $product->id }}" style="background:white; border-radius:14px; box-shadow:0 6px 22px #0002; overflow:hidden; border:1px solid #f3f4f6; text-decoration:none; cursor:pointer; display:flex; flex-direction:column;">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/400' }}" alt="{{ $product->name }}" style="width:100%; height:300px; object-fit:cover; display:block;">
                <div style="padding:12px 14px; text-align:left;">
                    <h3 style="margin:0 0 6px 0; font-size:1.05rem; font-weight:700; color:#2563eb;">{{ $product->name }}</h3>
                    <p style="font-size:1rem; font-weight:700; color:#222; margin:0;">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>
            </a>
        @endforeach
    </div>
@endsection
