<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
class AddressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)->get();
        $recentOrders = \App\Models\Order::where('user_id', $user->id)->with(['items.product'])->orderByDesc('created_at')->limit(5)->get();
        return view('address.index', compact('addresses', 'user', 'recentOrders'));
    }
    public function create()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)->get();
        $recentOrders = \App\Models\Order::where('user_id', $user->id)->with(['items.product'])->orderByDesc('created_at')->limit(5)->get();
        return view('address.create', compact('addresses', 'user', 'recentOrders'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);
        Address::create([
            'user_id' => Auth::id(),
            'recipient_name' => $request->recipient_name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        return redirect()->route('address.index');
    }
    public function edit($id)
    {
        $user = Auth::user();
        $address = Address::where('user_id', $user->id)->findOrFail($id);
        $addresses = Address::where('user_id', $user->id)->get();
        $recentOrders = \App\Models\Order::where('user_id', $user->id)->with(['items.product'])->orderByDesc('created_at')->limit(5)->get();
        return view('address.edit', compact('address', 'addresses', 'user', 'recentOrders'));
    }
    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $address->update($request->only(['recipient_name','phone','address']));
        return redirect()->route('address.index');
    }
    public function destroy($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $address->delete();
        return redirect()->route('address.index');
    }
}
