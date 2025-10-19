@extends('layouts.app')


@section('content')
@if(auth()->user()->role === 'user')
    <h2>Transaksi Saya</h2>
    <a href="/" style="display:inline-block; margin-bottom:18px; background:#e5e7eb; color:#222; border:none; padding:8px 20px; border-radius:6px; font-weight:500; text-decoration:none;">Back</a>
    @if(count($transactions) > 0)
        <table style="width:100%; border-collapse:collapse; margin-bottom:24px;">
            <thead>
                <tr style="background:#f3f4f6;">
                    <th>ID</th>
                    <th>Status</th>
                    <th>Metode</th>
                    <th>Tanggal Bayar</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $trx)
                    <tr>
                        <td>
                            <span>{{ $trx->id }}</span>
                            <a href="/transactions/{{ $trx->id }}" style="background:#2563eb; color:white; border:none; padding:4px 12px; border-radius:6px; font-size:0.95em; margin-left:8px; text-decoration:none;">Detail</a>
                        </td>
                        <td>{{ $trx->status }}</td>
                        <td>{{ $trx->payment_method }}</td>
                        <td>{{ $trx->paid_at }}</td>
                        <td>Rp {{ number_format($trx->order->total, 0, ',', '.') }}</td>
                                <td>
                                    @if($trx->status == 'belum_dibayar')
                                        <button class="btn-action" onclick="openTransactionModal({{ $trx->id }})">Bayar</button>
                                    @elseif($trx->status == 'dikirim')
                                        <button class="btn-action" onclick="openTransactionModal({{ $trx->id }})">Selesai</button>
                                    @else
                                        <button class="btn-action" onclick="openTransactionModal({{ $trx->id }})">Detail</button>
                                    @endif
                                </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Belum ada transaksi.</p>
    @endif
@endif
@endsection

@push('scripts')
<script>
function csrf(){const m=document.querySelector('meta[name="csrf-token"]');return m?m.getAttribute('content'):''}

async function openTransactionModal(id){
    // create modal if not exists
    let modal=document.getElementById('userTransactionModal');
    if(!modal){
        modal=document.createElement('div');modal.id='userTransactionModal';modal.className='modal';modal.style.display='none';modal.innerHTML=`<div class="modal-content"><button onclick="closeUserModal()" class="close">×</button><div id="userTransactionDetail"></div></div>`;document.body.appendChild(modal);
        const style=document.createElement('style');style.innerHTML='.modal{position:fixed;inset:0;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center}.modal-content{background:white;padding:20px;border-radius:8px;max-width:800px;width:100%}.close{position:absolute;right:16px;top:12px;border:none;background:transparent;font-size:20px}';document.head.appendChild(style);
    }
    modal.style.display='flex';
    const detail=document.getElementById('userTransactionDetail');detail.innerHTML='Loading...';
    try{
    const res=await fetch(`/transactions/${id}`, { headers: { 'Accept': 'application/json' } });
        if(!res.ok)throw new Error('Fetch failed');
        const trx=await res.json();
        // build detail
        const itemsHtml=trx.order.items.map(i=>`<tr><td>${i.product.name}</td><td>${i.quantity}</td><td>Rp ${Number(i.price).toLocaleString('id-ID')}</td><td>Rp ${(i.price*i.quantity).toLocaleString('id-ID')}</td></tr>`).join('');
        let actionHtml='';
        if(trx.status==='belum_dibayar'){
            actionHtml=`<div><label>Metode: <select id="pay_method"><option value="transfer">Bank Transfer</option><option value="cod">COD</option></select><button onclick="payTransaction(${trx.order.id}, ${trx.id})" class="btn-primary">Bayar</button></div>`;
        } else if(trx.status==='dikirim'){
            actionHtml=`<div><label>Pesanan sudah sampai?</label><button onclick="confirmReceived(${trx.id}, true)" class="btn-primary">Selesai</button><button onclick="confirmReceived(${trx.id}, false)" class="btn-secondary">Komplain</button></div>`;
        }
        detail.innerHTML=`<h3>Transaksi #${trx.id}</h3><p>Status: <strong>${trx.status}</strong></p><table class="items-table"><thead><tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Total</th></tr></thead><tbody>${itemsHtml}</tbody></table><div>${actionHtml}</div><div id="reviewSection"></div>`;
    }catch(e){detail.innerHTML='<p class="text-red">Gagal memuat transaksi</p>';console.error(e)}
}

function closeUserModal(){const m=document.getElementById('userTransactionModal');if(m)m.style.display='none'}

async function payTransaction(orderId, transactionId){
    const method=document.getElementById('pay_method').value;
    try{
    const res=await fetch(`/transactions/pay/${orderId}`,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({payment_method:method})});
        const data=await res.json();
        if(data.success){alert('Pembayaran berhasil');window.location.reload()}else{alert('Gagal membayar')}
    }catch(e){console.error(e);alert('Terjadi kesalahan')}
}

async function confirmReceived(transactionId, sesuai){
    try{
    const res=await fetch(`/transactions/confirm/${transactionId}`,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({sesuai: sesuai ? 'ya' : 'tidak'})});
        const data=await res.json();
        if(data.success){
            if(data.newStatus === 'selesai'){
                // fetch transaction details and show a review modal listing products
                const trxRes = await fetch(`/transactions/${transactionId}`, { headers: { 'Accept': 'application/json' } });
                if(trxRes.ok){
                    const trx = await trxRes.json();
                    showReviewListModal(trx);
                } else {
                    alert('Status diperbarui. Gagal memuat detail untuk review.');
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Status diperbarui');
                window.location.reload();
            }
        } else { alert('Gagal mengonfirmasi'); }
    }catch(e){console.error(e);alert('Terjadi kesalahan')}
}

async function submitReview(transactionId){
    // legacy: unused. Use submitReviewForProduct(productId, rating, comment) instead.
}

function showReviewListModal(trx){
    // Build modal listing products with review buttons
    let modal=document.getElementById('reviewListModal');
    if(!modal){
        modal=document.createElement('div');modal.id='reviewListModal';modal.className='modal';modal.style.display='none';
        modal.innerHTML=`<div class="modal-content"><button onclick="closeReviewListModal()" class="close">×</button><div id="reviewListContent"></div></div>`;document.body.appendChild(modal);
        const style=document.createElement('style');style.innerHTML='.modal{position:fixed;inset:0;background:rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center}.modal-content{background:white;padding:20px;border-radius:8px;max-width:800px;width:100%}.close{position:absolute;right:16px;top:12px;border:none;background:transparent;font-size:20px}';document.head.appendChild(style);
    }
    const content=document.getElementById('reviewListContent');
    const items = trx.order.items;
    let html = `<h3>Berikan Review untuk Produk</h3><div style="display:flex;flex-direction:column;gap:10px">`;
    for(const it of items){
        html += `<div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #eee;padding:8px 0;"><div><strong>${it.product.name}</strong><div style="font-size:13px;color:#666">Qty: ${it.quantity}</div></div><div><button class="btn-primary" onclick="openProductReviewForm(${it.product.id})">Berikan Review</button></div></div>`;
    }
    html += `</div>`;
    content.innerHTML = html;
    modal.style.display = 'flex';
}

function closeReviewListModal(){const m=document.getElementById('reviewListModal');if(m)m.style.display='none';}

function openProductReviewForm(productId){
    // reuse modal content to show review form for specific product
    const content=document.getElementById('reviewListContent');
    content.innerHTML = `<h3>Review Produk</h3><form id="singleReviewForm"><label>Rating: <select id="review_rating"><option value="5">5</option><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1</option></select></label><label style="display:block;margin-top:8px">Komentar:<textarea id="review_comment" rows="4" style="width:100%"></textarea></label><div style="margin-top:12px"><button type="button" onclick="submitReviewForProduct(${productId})" class="btn-primary">Kirim Review</button> <button type="button" onclick="showReviewListModalCached()" class="btn-secondary">Kembali</button></div></form>`;
}

// helper to re-show cached review list (fetch trx again if needed)
function showReviewListModalCached(){
    // close current product form and just reload the list by hiding modal; user can re-open modal from transaction list
    const trxModal=document.getElementById('reviewListModal');
    if(trxModal){
        // simple approach: close modal, user can re-open from order modal
        trxModal.style.display='none';
    }
}

async function submitReviewForProduct(productId){
    const rating = document.getElementById('review_rating').value;
    const comment = document.getElementById('review_comment').value;
    try{
        const res = await fetch(`/products/${productId}/review`,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf()},body:JSON.stringify({rating:rating,comment:comment})});
        const data = await res.json();
        if(res.ok && data.success){
            // redirect to product page so user sees their review
            window.location.href = `/products/${productId}`;
        } else if(!res.ok){
            alert(data?.message || 'Gagal mengirim review');
        }
    }catch(e){console.error(e);alert('Gagal mengirim review')}
}

// close modal clicking outside
window.addEventListener('click',function(e){const m=document.getElementById('userTransactionModal');if(m && e.target===m) m.style.display='none'});
</script>
@endpush

