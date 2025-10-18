<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\User;
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // load user's addresses and a small recent orders summary
        $addresses = \App\Models\Address::where('user_id', $user->id)->get();
        $recentOrders = Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('user', 'addresses', 'recentOrders'));
    }
    public function account()
    {
        $user = Auth::user();
        $addresses = \App\Models\Address::where('user_id', $user->id)->get();
        $recentOrders = Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard.account', compact('user', 'addresses', 'recentOrders'));
    }
    public function updateAccount(Request $request)
    {
    $data = [];
    if ($request->filled('username')) $data['username'] = $request->username;
    if ($request->filled('name')) $data['name'] = $request->name;
    if ($request->filled('phone')) $data['phone'] = $request->phone;
    if ($request->filled('email')) $data['email'] = $request->email;
    if ($request->filled('password')) $data['password'] = bcrypt($request->password);

    // handle avatar upload
    if ($request->hasFile('avatar')) {
        $request->validate(['avatar' => 'image|max:2048']);
        $path = $request->file('avatar')->store('avatars', 'public');
        $data['avatar'] = $path;
    }

    if (!empty($data)) {
        DB::table('users')->where('id', Auth::id())->update($data);
    }

    return redirect()->route('dashboard')->with('success','Profil berhasil diupdate');
    }
    public function orders(Request $request)
    {
        $status = $request->query('status');
        $orders = Order::where('user_id', Auth::id())
            ->when($status, function($q) use ($status) {
                $q->where('status', $status);
            })
            ->with(['items.product'])
            ->orderByDesc('created_at')
            ->get();
        return view('dashboard.orders', compact('orders'));
    }
}
