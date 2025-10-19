@extends('admin.layout')

@section('title', 'Manajemen Transaksi')

@section('content')
<h1 class="page-title">Manajemen Transaksi</h1>

<div class="section">
    <h2 class="section-title">Daftar Transaksi</h2>
    <table>
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Tanggal</th>
                <th>Pembeli</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->order->order_number }}</td>
                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                <td>{{ $transaction->order->user->name }}</td>
                <td>Rp {{ number_format($transaction->amount) }}</td>
                <td>
                    <span class="status-badge status-{{ $transaction->status }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </td>
                <td>
                    <button class="btn-action btn-edit" onclick="showTransactionDetail({{ $transaction->id }})">Detail</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Transaction Detail -->
<div id="transactionModal" class="modal" style="display: none;">
    <div class="modal-content max-w-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Detail Transaksi</h3>
            <button onclick="hideTransactionModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div id="transactionDetail" class="space-y-4">
            <!-- Will be filled by JavaScript -->
        </div>
        <div class="mt-6 flex justify-end">
            <button type="button" onclick="hideTransactionModal()" class="btn-secondary">Tutup</button>
        </div>
    </div>
</div>

<style>
/* abbreviated styles retained from previous file */
.status-badge{padding:4px 8px;border-radius:4px;font-size:12px;font-weight:500}
.items-table{width:100%;margin-top:10px;font-size:14px}
.detail-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}
.detail-item{padding:10px;background:#f8fafc;border-radius:6px}
.form-actions{display:flex;justify-content:flex-end;margin-top:20px}
.btn-secondary{padding:8px 16px;background:#e5e7eb;color:#4b5563;border:none;border-radius:6px;cursor:pointer}
</style>

@endsection

@push('scripts')
<script>
function numberFormat(number){return new Intl.NumberFormat('id-ID').format(number)}
function formatDate(dateString){return new Date(dateString).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'})}

async function showTransactionDetail(transactionId){
    const modal=document.getElementById('transactionModal');
    const detailContainer=document.getElementById('transactionDetail');
    modal.style.display='flex';
    detailContainer.innerHTML='<p>Loading...</p>';
    try{
        const res=await fetch(`/admin/transactions/${transactionId}`);
        if(!res.ok)throw new Error('Failed to fetch');
        const t=await res.json();
        const order=t.order;
        const itemsHtml=order.items.map(i=>`<tr><td>${i.product.name}</td><td>${i.quantity}</td><td>Rp ${numberFormat(i.price)}</td><td>Rp ${numberFormat(i.price*i.quantity)}</td></tr>`).join('');
        let actionHtml='';
        if(t.status==='disiapkan'){
            actionHtml=`<button class="btn-primary" onclick="updateStatus(${t.id})">Kirim Pesanan</button>`;
        }
        detailContainer.innerHTML=`<div class="detail-section"><h4>Informasi Transaksi</h4><div class="detail-grid"><div class="detail-item"><p>No. Order</p><p>${order.order_number}</p></div><div class="detail-item"><p>Tanggal</p><p>${formatDate(t.created_at)}</p></div><div class="detail-item"><p>Status</p><p>${t.status}</p></div><div class="detail-item"><p>Total</p><p>Rp ${numberFormat(t.amount)}</p></div></div><div class="form-actions">${actionHtml}</div></div><div class="detail-section"><h4>Informasi Pembeli</h4><div class="detail-grid"><div class="detail-item"><p>Nama</p><p>${order.user.name}</p></div><div class="detail-item"><p>Email</p><p>${order.user.email}</p></div></div></div><div class="detail-section"><h4>Daftar Produk</h4><table class="items-table"><thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Total</th></tr></thead><tbody>${itemsHtml}</tbody></table></div>`;
    }catch(err){detailContainer.innerHTML='<p class="text-red-500">Gagal memuat detail transaksi</p>';console.error(err)}
}
function hideTransactionModal(){document.getElementById('transactionModal').style.display='none'}
function updateStatus(transactionId){if(!confirm('Apakah Anda yakin ingin mengirim pesanan ini?'))return;fetch(`/admin/transactions/${transactionId}/status`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content'),'Accept':'application/json'}}).then(r=>r.json()).then(data=>{if(data.success){alert(data.message);window.location.reload()}else{alert(data.message||'Gagal mengubah status')}}).catch(e=>{console.error(e);alert('Terjadi kesalahan')})}
window.addEventListener('click',function(e){const modal=document.getElementById('transactionModal');if(e.target===modal)modal.style.display='none'})
</script>
@endpush