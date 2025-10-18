@extends('layouts.app')

@section('content')
    <h2>Tambah Produk</h2>
    <form method="POST" action="/products" enctype="multipart/form-data" style="max-width:500px; margin:auto; background:white; border-radius:12px; box-shadow:0 2px 12px #0002; padding:32px;">
        @csrf
        <div style="margin-bottom:16px;">
            <label for="name">Nama Produk <span style="color:red">*</span></label><br>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div style="margin-bottom:16px;">
            <label for="price">Harga <span style="color:red">*</span></label><br>
            <input type="number" name="price" id="price" class="form-control" required step="1000">
        </div>
        <div style="margin-bottom:16px;">
            <label for="stock">Stok <span style="color:red">*</span></label><br>
            <input type="number" name="stock" id="stock" class="form-control" required>
        </div>
        <div style="margin-bottom:16px;">
            <label for="category_id">Kategori <span style="color:red">*</span></label><br>
            <select name="category_id" id="category_id" class="form-control" required>
                @foreach(\App\Models\Category::all() as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="margin-bottom:16px;">
            <label for="image">Gambar Produk</label><br>
            <input type="file" name="image" id="image" class="form-control">
        </div>
        <button type="submit" style="background:#2563eb; color:white; border:none; padding:10px 24px; border-radius:6px; font-weight:500;">Tambah Produk</button>
    </form>
@endsection
