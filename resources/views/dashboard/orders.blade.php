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
                            @if($first && $first->product)
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
                            @endif
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
                            @if($first && $first->product)
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
                            @endif
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
                            @if($first && $first->product)
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
                            @endif
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
                            @if($first && $first->product)
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
                            @endif
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
                            @if($first && $first->product)
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
                            @endif
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

<script>
function csrf(){const m=document.querySelector('meta[name="csrf-token"]');return m?m.getAttribute('content'):''}

document.addEventListener('click', function(e){
    if(e.target && e.target.id && e.target.id.startsWith('open-review-modal-')){
        e.preventDefault();
        const id = e.target.id.replace('open-review-modal-','');
        console.log('Opening review modal for order:', id);
        openOrderReviewModal(id);
    }
});

async function openOrderReviewModal(orderId){
    console.log('Fetching order:', orderId);
    try{
        const res = await fetch(`/orders/${orderId}`, { 
            headers: { 
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            } 
        });
        if(!res.ok) throw new Error('Failed to load order');
        const order = await res.json();
        console.log('Order loaded:', order);
        showOrderReviewModal(order);
    }catch(e){
        console.error('Error loading order:', e);
        alert('Gagal memuat detail pesanan untuk review. Error: ' + e.message);
    }
}

function showOrderReviewModal(order){
    let modal=document.getElementById('orderReviewModal');
    if(!modal){
        modal=document.createElement('div');
        modal.id='orderReviewModal';
        modal.className='modal';
        modal.style.display='none';
        modal.innerHTML=`
            <div class="modal-content relative bg-white rounded-lg shadow">
                <button onclick="closeOrderReviewModal()" class="close absolute right-4 top-4 text-gray-600 hover:text-gray-800">×</button>
                <div id="orderReviewContent" class="p-6"></div>
            </div>
        `;
        document.body.appendChild(modal);
        const style=document.createElement('style');
        style.innerHTML=`
            .modal{position:fixed;inset:0;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;z-index:1000}
            .modal-content{max-width:800px;width:100%;margin:20px}
            .close{font-size:24px;cursor:pointer;border:none;background:transparent}
            .btn-primary{background:#4F46E5;color:white;padding:8px 16px;border-radius:6px;border:none;cursor:pointer}
            .btn-primary:hover{background:#4338CA}
            .btn-secondary{background:#9CA3AF;color:white;padding:8px 16px;border-radius:6px;border:none;cursor:pointer;margin-left:8px}
            .btn-secondary:hover{background:#6B7280}
        `;
        document.head.appendChild(style);
    }
    const content=document.getElementById('orderReviewContent');
    let html=`
        <div class="mb-6">
            <h3 class="text-xl font-bold mb-4">Berikan Review</h3>
            <div class="space-y-4">`;
    for(const item of order.items){
        html += `
            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900">${item.product.name}</h4>
                    <p class="text-sm text-gray-600">Jumlah: ${item.quantity}</p>
                </div>
                <div>
                    <button class="btn-primary" onclick="openProductReviewFormFromOrder(${item.product.id}, ${order.id})">
                        Berikan Review
                    </button>
                </div>
            </div>`;
    }
    html += `</div></div>`;
    content.innerHTML = html;
    modal.style.display='flex';
}

function closeOrderReviewModal(){const m=document.getElementById('orderReviewModal');if(m)m.style.display='none'}

function openProductReviewFormFromOrder(productId, orderId){
    const content=document.getElementById('orderReviewContent');
    content.innerHTML = `
        <div class="mb-6">
            <h3 class="text-xl font-bold mb-4">Review Produk</h3>
            <form id="singleReviewFormOrder" class="space-y-4">
                <input type="hidden" id="review_order_id_order" value="${orderId}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating:</label>
                    <select id="review_rating_order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                        <option value="4">⭐⭐⭐⭐ (4)</option>
                        <option value="3">⭐⭐⭐ (3)</option>
                        <option value="2">⭐⭐ (2)</option>
                        <option value="1">⭐ (1)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Komentar:</label>
                    <textarea id="review_comment_order" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Bagikan pengalaman Anda dengan produk ini..."></textarea>
                </div>
                <div class="flex justify-end space-x-2 pt-4">
                    <button type="button" onclick="submitReviewForProductFromOrder(${productId})" class="btn-primary">Kirim Review</button>
                    <button type="button" onclick="openOrderReviewModal(${orderId})" class="btn-secondary">Kembali</button>
                </div>
            </form>
        </div>`;
}

async function submitReviewForProductFromOrder(productId){
    const form = document.getElementById('singleReviewFormOrder');
    const submitBtn = form.querySelector('button[type="button"]');
    const rating = document.getElementById('review_rating_order').value;
    const comment = document.getElementById('review_comment_order').value;
    const orderId = document.getElementById('review_order_id_order').value;
    
    if(!rating || !comment.trim()) {
        alert('Rating dan komentar harus diisi');
        return;
    }
    
    try{
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Mengirim...';
        
        const res = await fetch(`/products/${productId}/review`,{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN':csrf()
            },
            body:JSON.stringify({rating:rating,comment:comment,order_id:orderId})
        });
        
        const data = await res.json();
        
        if(res.ok && data.success){
            alert('Review berhasil dikirim!');
            closeOrderReviewModal();
            window.location.reload();
        } else {
            alert(data?.message || 'Gagal mengirim review. Silakan coba lagi.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Kirim Review';
        }
    }catch(e){
        console.error(e);
        alert('Terjadi kesalahan. Silakan coba lagi.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Kirim Review';
    }
}
</script>