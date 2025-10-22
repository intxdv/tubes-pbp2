<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // Tampilkan semua produk
    public function index()
    {
        // Redirect admin to admin dashboard if trying to access homepage
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        $products = Product::all();
        return view('home', compact('products'));
    }

    // Tampilkan detail produk
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('product.show', compact('product'));
    }

    // Search / filter products (server-backed)
    public function search(Request $request)
    {
    // accept both 'q' (new) and legacy 'search' param (from home form)
    $q = $request->query('q', $request->query('search'));
        $min = $request->query('min_price');
        $max = $request->query('max_price');
        $categories = $request->query('categories', []);
        $sort = $request->query('sort');

        $query = Product::query();

        if ($q) {
            $query->where(function($qr) use ($q) {
                $qr->where('name', 'like', "%{$q}%")
                   ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($min !== null && $min !== '') { $query->where('price', '>=', floatval($min)); }
        if ($max !== null && $max !== '') { $query->where('price', '<=', floatval($max)); }

        if (!empty($categories)) {
            // categories could be comma-separated or array
            if (is_string($categories)) {
                $categories = explode(',', $categories);
            }
            // if categories look numeric, treat them as category IDs
            $allNumeric = count($categories) > 0 && collect($categories)->every(function($c){ return is_numeric($c); });
            if ($allNumeric) {
                $ids = array_map('intval', $categories);
                $query->whereIn('category_id', $ids);
            } else {
                // fallback: match by category name
                $query->whereHas('category', function($qcat) use ($categories) {
                    $qcat->whereIn('name', $categories);
                });
            }
        }

        // also support legacy single category id param (from home page select)
        if ($request->filled('category')) {
            $query->where('category_id', $request->query('category'));
        }

        // sorting
        switch ($sort) {
            case 'price-low': $query->orderBy('price', 'asc'); break;
            case 'price-high': $query->orderBy('price', 'desc'); break;
            case 'rating': // assume reviews relationship and an average column not available; fallback to id
                $query->orderBy('id', 'desc'); break;
            case 'newest': $query->orderBy('id', 'desc'); break;
            default: // relevant / default
                break;
        }

        $products = $query->paginate(24)->appends($request->query());

        return view('products.search', compact('products'));
    }

    // Form tambah produk
    public function create()
    {
        return view('product.create');
    }

    // Simpan produk baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        $data['description'] = $request->input('description', '');
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }
        Product::create($data);
        return redirect('/products');
    }

    // Form edit produk
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('product.edit', compact('product'));
    }

    // Update produk
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $data = $validated;
        $data['description'] = $request->input('description', $product->description);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }
        $product->update($data);
        return redirect('/products');
    }

    // Hapus produk
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        if (\Illuminate\Support\Facades\Auth::user() && \Illuminate\Support\Facades\Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard');
        }
        return redirect('/products');
    }
}
