@php
    // Ensure $orders is defined whether this partial is included from /dashboard (uses $recentOrders)
    if (!isset($orders)) {
        $orders = $recentOrders ?? \App\Models\Order::where('user_id', auth()->id())->with(['items.product'])->orderByDesc('created_at')->get();
    }
@endphp

<div id="orders" class="content-section hidden">
    <h2 class="text-3xl font-bold mb-4">Pesanan Saya</h2>
    <div class="bg-white rounded-lg shadow-md">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex space-x-4 p-4" id="orderTabs">
                <button data-target="semua" class="tab-btn py-2 px-4 text-gray-600 hover:text-blue-500 focus:outline-none tab-active">Semua</button>
                <button data-target="belum-bayar" class="tab-btn py-2 px-4 text-gray-600 hover:text-blue-500 focus:outline-none">Belum Dibayar</button>
                <button data-target="disiapkan" class="tab-btn py-2 px-4 text-gray-600 hover:text-blue-500 focus:outline-none">Disiapkan</button>
                <button data-target="otw" class="tab-btn py-2 px-4 text-gray-600 hover:text-blue-500 focus:outline-none">Pesanan OTW</button>
                <button data-target="selesai" class="tab-btn py-2 px-4 text-gray-600 hover:text-blue-500 focus:outline-none">Pesanan Selesai</button>
            </nav>
        </div>

        <div class="p-6">
            <div id="semua" class="tab-content">
                @if($orders->isEmpty())
                    <p class="text-gray-600">Belum ada pesanan.</p>
                @else
                    <div class="space-y-4">
                        @foreach($orders as $order)
                            @php $first = $order->items->first(); $count = $order->items->count(); @endphp
                            <div class="flex items-center justify-between border rounded p-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $first->product->image_url ?? '/images/product-default.png' }}" class="w-20 h-20 object-cover rounded">
                                    <div>
                                        <div class="font-semibold">{{ $first->product->name }}@if($count>1) +{{ $count-1 }} lainnya @endif</div>
                                        <div class="text-sm text-gray-500">Jumlah: {{ $order->items->sum('quantity') }} • Total: Rp {{ number_format($order->total,0,',','.') }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-blue-600">{{ ucfirst($order->status) }}</div>
                                    <div class="mt-2"><a href="/orders/{{ $order->id }}" class="bg-gray-200 text-gray-800 px-3 py-1 rounded">Detail</a></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="belum-bayar" class="tab-content hidden">
                @php $filtered = $orders->where('status','belum_dibayar'); @endphp
                @if($filtered->isEmpty())
                    <p class="text-gray-600">Tidak ada pesanan belum dibayar.</p>
                @else
                    <div class="space-y-4">
                        @foreach($filtered as $order)
                            @php $first = $order->items->first(); $count = $order->items->count(); @endphp
                            <div class="flex items-center justify-between border rounded p-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $first->product->image_url ?? '/images/product-default.png' }}" class="w-20 h-20 object-cover rounded">
                                    <div>
                                        <div class="font-semibold">{{ $first->product->name }}@if($count>1) +{{ $count-1 }} lainnya @endif</div>
                                        <div class="text-sm text-gray-500">Jumlah: {{ $order->items->sum('quantity') }} • Total: Rp {{ number_format($order->total,0,',','.') }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-red-600">{{ ucfirst($order->status) }}</div>
                                    <div class="mt-2"><a href="/orders/{{ $order->id }}" class="bg-gray-200 text-gray-800 px-3 py-1 rounded">Bayar</a></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="disiapkan" class="tab-content hidden">
                @php $filtered = $orders->where('status','disiapkan'); @endphp
                @if($filtered->isEmpty())
                    <p class="text-gray-600">Tidak ada pesanan disiapkan.</p>
                @else
                    <div class="space-y-4">
                        @foreach($filtered as $order)
                            @php $first = $order->items->first(); $count = $order->items->count(); @endphp
                            <div class="flex items-center justify-between border rounded p-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $first->product->image_url ?? '/images/product-default.png' }}" class="w-20 h-20 object-cover rounded">
                                    <div>
                                        <div class="font-semibold">{{ $first->product->name }}@if($count>1) +{{ $count-1 }} lainnya @endif</div>
                                        <div class="text-sm text-gray-500">Jumlah: {{ $order->items->sum('quantity') }} • Total: Rp {{ number_format($order->total,0,',','.') }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-gray-600">{{ ucfirst($order->status) }}</div>
                                    <div class="mt-2"><a href="/orders/{{ $order->id }}" class="bg-gray-200 text-gray-800 px-3 py-1 rounded">Detail</a></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="otw" class="tab-content hidden">
                @php $filtered = $orders->where('status','dikirim'); @endphp
                @if($filtered->isEmpty())
                    <p class="text-gray-600">Tidak ada pesanan dalam perjalanan.</p>
                @else
                    <div class="space-y-4">
                        @foreach($filtered as $order)
                            @php $first = $order->items->first(); $count = $order->items->count(); @endphp
                            <div class="flex items-center justify-between border rounded p-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $first->product->image_url ?? '/images/product-default.png' }}" class="w-20 h-20 object-cover rounded">
                                    <div>
                                        <div class="font-semibold">{{ $first->product->name }}@if($count>1) +{{ $count-1 }} lainnya @endif</div>
                                        <div class="text-sm text-gray-500">Jumlah: {{ $order->items->sum('quantity') }} • Total: Rp {{ number_format($order->total,0,',','.') }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-orange-600">{{ ucfirst($order->status) }}</div>
                                    <div class="mt-2"><a href="/orders/{{ $order->id }}" class="bg-gray-200 text-gray-800 px-3 py-1 rounded">Detail</a></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div id="selesai" class="tab-content hidden">
                @php $filtered = $orders->where('status','selesai'); @endphp
                @if($filtered->isEmpty())
                    <p class="text-gray-600">Tidak ada pesanan selesai.</p>
                @else
                    <div class="space-y-4">
                        @foreach($filtered as $order)
                            @php $first = $order->items->first(); $count = $order->items->count(); @endphp
                            <div class="flex items-center justify-between border rounded p-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $first->product->image_url ?? '/images/product-default.png' }}" class="w-20 h-20 object-cover rounded">
                                    <div>
                                        <div class="font-semibold">{{ $first->product->name }}@if($count>1) +{{ $count-1 }} lainnya @endif</div>
                                        <div class="text-sm text-blue-500">Pesanan #{{ $order->id }} - Selesai pada {{ optional($order->updated_at)->format('d M Y') }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button id="open-review-modal-{{ $order->id }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Beri Rating & Review</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Minimal JS to switch tabs -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('#orderTabs .tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                tabButtons.forEach(btn => btn.classList.remove('tab-active'));
                button.classList.add('tab-active');

                const target = button.getAttribute('data-target');
                tabContents.forEach(content => content.classList.add('hidden'));
                const el = document.getElementById(target);
                if (el) el.classList.remove('hidden');
            });
        });
    });
</script>

<!-- end orders -->
