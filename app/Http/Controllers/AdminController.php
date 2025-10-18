<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
class AdminController extends Controller
{
    // Dashboard admin
    public function dashboard()
    {
        $orders = Order::where('status', '!=', 'cart')->get();
        $products = Product::all();
        $transactions = Transaction::all();
        return view('admin.dashboard', compact('orders', 'products', 'transactions'));
    }

    // Kelola produk (CRUD sudah di ProductController)
    // Kelola transaksi
    public function transactions()
    {
        $transactions = Transaction::all();
        return view('admin.transactions', compact('transactions'));
    }

    // Tampilkan kategori
    public function categories()
    {
        $categories = \App\Models\Category::all();
        return view('admin.categories', compact('categories'));
    }

    // Tambah kategori
    public function addCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        \App\Models\Category::create(['name' => $request->name]);
        return redirect('/admin/categories')->with('success', 'Kategori berhasil ditambahkan');
    }

    // Hapus kategori
    public function deleteCategory($id)
    {
        $cat = \App\Models\Category::findOrFail($id);
        $cat->deleteWithProducts();
        return redirect('/admin/categories')->with('success', 'Kategori berhasil dihapus');
    }
}
