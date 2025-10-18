<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $orders = \App\Models\Order::where('status', '!=', 'cart')->get();
            $products = \App\Models\Product::all();
            $transactions = \App\Models\Transaction::all();
            return view('admin.dashboard', compact('orders', 'products', 'transactions'));
        }
        return view('profile.index');
    }
}
