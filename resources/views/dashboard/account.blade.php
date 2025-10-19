<!-- Dashboard Home Section (two-column layout) -->
<div id="dashboard-home" class="content-section px-2 md:px-0">
    <h2 class="text-3xl font-bold mb-4">Dashboard</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left: Profile edit (2 cols on md) -->
        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
            <form method="POST" action="{{ route('dashboard.account.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 gap-4">
                    <div class="flex items-center gap-4">
                        @php $avatar = auth()->user()->avatar ?? 'images/profile.png'; @endphp
                        <img src="{{ auth()->user()->avatar ? asset('storage/'.auth()->user()->avatar) : asset($avatar) }}" class="w-24 h-24 rounded-full object-cover">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Ganti Avatar</label>
                            <input type="file" name="avatar" accept="image/*" class="border p-2 rounded">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700">
                        </div>
                        @php $hasUsername = \Illuminate\Support\Facades\Schema::hasColumn('users', 'username'); @endphp
                        @if($hasUsername)
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Username</label>
                            <input type="text" name="username" value="{{ old('username', auth()->user()->username) }}" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700">
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">No. Telp</label>
                            <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-1">Password (isi jika ingin ganti)</label>
                        <input type="password" name="password" class="shadow-sm border rounded w-full py-2 px-3 text-gray-700">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right: Addresses pane -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Alamat</h3>
                <a href="{{ route('address.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-semibold py-1 px-3 rounded">Tambah</a>
            </div>
            @if (session('success'))
                <div class="mb-3 rounded border border-green-300 bg-green-50 text-green-800 text-sm px-3 py-2">
                    {{ session('success') }}
                </div>
            @endif
                <div class="space-y-4">
                    @if(isset($addresses) && $addresses->isNotEmpty())
                        <div class="max-h-72 overflow-auto space-y-3">
                            @foreach($addresses as $addr)
                                <div class="border p-3 rounded bg-white">
                                    <div class="flex justify-between items-start gap-2">
                                        <div>
                                            <div class="font-semibold">{{ $addr->recipient_name }}</div>
                                            <div class="text-sm text-gray-600 mt-1">{{ $addr->address }}</div>
                                            <div class="text-sm text-gray-500 mt-1">{{ $addr->phone }}</div>
                                        </div>
                                        <div class="text-right">
                                            <a href="{{ route('address.edit', $addr->id) }}" class="text-blue-500 text-sm hover:underline">Edit</a>
                                            <form action="{{ route('address.destroy', $addr->id) }}" method="POST" style="display:inline-block">@csrf @method('DELETE')<button class="text-red-500 text-sm hover:underline ml-2">Hapus</button></form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-gray-600">Belum ada alamat tersimpan.</div>
                    @endif
                </div>
        </div>
    </div>

    <!-- Orders summary table below -->
    <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold mb-4">Riwayat Pesanan</h3>
        @php
            $orders = \App\Models\Order::where('user_id', auth()->id())->with(['items.product'])->orderByDesc('created_at')->get();
        @endphp
        @if($orders->isEmpty())
            <div class="text-gray-600">Belum ada pesanan.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                            @php $first = $order->items->first(); $count = $order->items->count(); $qty = $order->items->sum('quantity'); @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $first->product->name }}@if($count>1) +{{ $count-1 }} more @endif</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $qty }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($order->total,0,',','.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-semibold">{{ ucfirst($order->status) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"><a href="/orders/{{ $order->id }}" class="text-indigo-600 hover:text-indigo-900">Detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- end dashboard-home -->
