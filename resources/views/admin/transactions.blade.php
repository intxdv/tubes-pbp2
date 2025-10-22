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
                    <button class="btn-action btn-edit" onclick="showTransactionModal({{ $transaction->id }})">Detail</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Transaction Detail -->
{{-- unified modal (styled like add-category modal) for transaction details --}}
<div id="transactionModal" class="modal" style="display: none;">
    <div class="modal-content max-w-2xl">
        <h3 id="transactionModalTitle">Detail Transaksi</h3>

        <div id="transactionDetail" class="space-y-4">
            <!-- populated by JS: sections for info, buyer, items -->
        </div>

        <div class="form-actions mt-6">
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

/* modal styles (copied from products view) */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 700px;
}

.modal-content h3 {
    margin-bottom: 20px;
    color: #2c3e50;
}

.btn-primary {
    padding: 8px 16px;
    background-color: #2563eb;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.btn-primary:hover {
    background-color: #1e40af;
}
</style>

@endsection

@push('scripts')
<script>
function numberFormat(number){return new Intl.NumberFormat('id-ID').format(number)}
function formatDate(dateString){return new Date(dateString).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'})}

// show transaction modal (similar pattern to product edit modal)
function showTransactionModal(transactionId) {
    const modal = document.getElementById('transactionModal');
    const title = document.getElementById('transactionModalTitle');
    const detailContainer = document.getElementById('transactionDetail');
    const actionBtn = document.getElementById('transactionActionBtn');

    title.textContent = 'Detail Transaksi #' + transactionId;
    detailContainer.innerHTML = '<p>Loading...</p>';
    actionBtn.style.display = 'none';
    modal.style.display = 'flex';

    fetch(`/admin/transactions/${transactionId}`)
        .then(res => { if(!res.ok) throw new Error('Failed to fetch'); return res.json() })
        .then(t => {
            const order = t.order;
            const itemsHtml = order.items.map(i => `<tr><td>${i.product.name}</td><td>${i.quantity}</td><td>Rp ${numberFormat(i.price)}</td><td>Rp ${numberFormat(i.price*i.quantity)}</td></tr>`).join('');
            let adminAction = '';
            if (t.status === 'disiapkan') {
                adminAction = `<button class="btn-primary" onclick="updateStatus(${t.id})">Kirim Pesanan</button>`;
                // also show primary action button
                actionBtn.style.display = 'inline-block';
                actionBtn.textContent = 'Kirim Pesanan';
                actionBtn.onclick = function(){ updateStatus(t.id) };
            } else {
                actionBtn.style.display = 'none';
            }

            detailContainer.innerHTML = `
                <div class="detail-section">
                    <h4>Informasi Transaksi</h4>
                    <div class="detail-grid">
                        <div class="detail-item"><p>No. Order</p><p>${order.order_number}</p></div>
                        <div class="detail-item"><p>Tanggal</p><p>${formatDate(t.created_at)}</p></div>
                        <div class="detail-item"><p>Status</p><p>${t.status}</p></div>
                        <div class="detail-item"><p>Total</p><p>Rp ${numberFormat(t.amount)}</p></div>
                    </div>
                    <div class="form-actions">${adminAction}</div>
                </div>
                <div class="detail-section">
                    <h4>Informasi Pembeli</h4>
                    <div class="detail-grid">
                        <div class="detail-item"><p>Nama</p><p>${order.user.name}</p></div>
                        <div class="detail-item"><p>Email</p><p>${order.user.email}</p></div>
                    </div>
                </div>
                <div class="detail-section">
                    <h4>Daftar Produk</h4>
                    <table class="items-table">
                        <thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Total</th></tr></thead>
                        <tbody>${itemsHtml}</tbody>
                    </table>
                </div>
            `;
        })
        .catch(err => {
            detailContainer.innerHTML = '<p class="text-red-500">Gagal memuat detail transaksi</p>';
            console.error(err);
        });
}

function hideTransactionModal(){document.getElementById('transactionModal').style.display='none'}
function updateStatus(transactionId){if(!confirm('Apakah Anda yakin ingin mengirim pesanan ini?'))return;fetch(`/admin/transactions/${transactionId}/status`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content'),'Accept':'application/json'}}).then(r=>r.json()).then(data=>{if(data.success){alert(data.message);window.location.reload()}else{alert(data.message||'Gagal mengubah status')}}).catch(e=>{console.error(e);alert('Terjadi kesalahan')})}
window.addEventListener('click',function(e){const modal=document.getElementById('transactionModal');if(e.target===modal)modal.style.display='none'})
</script>
@endpush