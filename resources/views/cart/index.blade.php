@extends('layouts.app')

@section('content')

<style>
    /* Scoped styles for cart page (adapted from provided template) */
    .content { max-width: 1200px; margin: 24px auto; padding: 24px; }
    .controls-bar{ display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; padding:14px; background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
    .select-all-container{ display:flex; align-items:center; gap:12px; }
    .checkbox-custom{ width:20px; height:20px; cursor:pointer; }
    .main-grid{ display:grid; grid-template-columns:1fr 360px; gap:24px; }
    .cart-items{ display:flex; flex-direction:column; gap:18px; }
    .cart-item{ display:grid; grid-template-columns:auto 120px 1fr auto; gap:18px; padding:22px; background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.04); transition:all .18s ease; }
    .cart-item:hover{ transform:translateY(-4px); box-shadow:0 8px 28px rgba(0,0,0,0.06); }
    .item-checkbox{ display:flex; align-items:center; }
    .item-image{ width:120px; height:120px; border-radius:10px; object-fit:cover; background:#f3f4f6; }
    .item-details{ display:flex; flex-direction:column; justify-content:space-between; }
    .item-name{ font-size:1.1rem; font-weight:600; color:#2c3e50; margin-bottom:6px; }
    .item-price{ color:#6b7280; font-size:0.98rem; margin-bottom:10px; }
    .quantity-controls{ display:flex; align-items:center; gap:10px; }
    .quantity-controls button{ width:36px; height:36px; border:2px solid #e5e7eb; background:#fff; color:#2c3e50; border-radius:8px; cursor:pointer; font-size:18px; }
    .quantity-controls input{ width:80px; height:36px; text-align:center; border:2px solid #e5e7eb; border-radius:8px; font-size:16px; }
    .item-actions{ display:flex; flex-direction:column; justify-content:space-between; align-items:flex-end; }
    .item-subtotal{ font-size:1.1rem; font-weight:700; color:#2c3e50; }
    .btn-remove{ background:#ef4444; color:white; border:none; padding:10px 14px; border-radius:10px; }
    .cart-summary{ background:#fff; padding:18px; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.04); position:sticky; top:120px; display:flex; flex-direction:column; gap:12px; }
    .summary-title{ font-weight:700; font-size:1.15rem; margin-bottom:6px; }
    .summary-box{ background:#f8fafc; padding:12px; border-radius:10px; border:1px solid #eef2f7; }
    .summary-row{ display:flex; justify-content:space-between; padding:8px 0; color:#374151; }
    .summary-row.total{ font-weight:800; font-size:1.05rem; margin-top:8px; }
    .checkout-btn{ width:100%; padding:12px; background:#2c3e50; color:#fff; border-radius:10px; border:none; font-weight:700; cursor:pointer; }
    .checkout-meta{ display:flex; flex-direction:column; gap:8px; }
    @media (max-width:900px){ .main-grid{ grid-template-columns:1fr; } .cart-summary{ position:static; } }
</style>

<div class="content">
        <div class="controls-bar">
            <div class="select-all-container">
                <input type="checkbox" id="selectAll" class="checkbox-custom" onchange="toggleSelectAll()">
                <label for="selectAll" class="select-all-label">Pilih Semua</label>
            </div>
            <div style="display:flex; gap:8px; align-items:center;">
                <a href="/" style="background:#fff; color:#2563eb; padding:8px 12px; border-radius:8px; text-decoration:none; font-weight:600; border:1px solid #eef2f7;">Beranda</a>
            </div>
        </div>

        {{-- Server-rendered cart form: submit here to /cart/checkout --}}
        <form method="POST" action="/cart/checkout" id="cart-checkout-form">
            @csrf

            <div class="main-grid">
                <div class="cart-items">
                    @php
                        // normalize items for rendering
                        $normalized = [];
                        foreach (($items ?? []) as $it) {
                            if (is_array($it)) {
                                $prod = $it['product'] ?? null;
                                $id = $it['product_id'] ?? ($prod['id'] ?? ($it['id'] ?? null));
                                $name = $it['name'] ?? ($prod['name'] ?? 'Item');
                                $price = $it['price'] ?? 0;
                                $qty = $it['quantity'] ?? 1;
                                $image = $it['image'] ?? null;
                            } else {
                                $prod = isset($it->product) ? $it->product : null;
                                $id = $it->product_id ?? ($prod->id ?? ($it->id ?? null));
                                $name = $prod->name ?? ($it->name ?? 'Item');
                                $price = $it->price ?? 0;
                                $qty = $it->quantity ?? ($it->qty ?? 1);
                                $image = $prod->image ?? null;
                            }
                            $normalized[] = ['id'=>$id,'name'=>$name,'price'=>$price,'quantity'=>$qty,'image'=>$image];
                        }
                    @endphp

                    @if(count($normalized) === 0)
                        <div class="cart-empty">
                            <div class="cart-empty-icon">🛒</div>
                            <h2 style="color:#4a5568; margin-bottom:10px;">Keranjang Belanja Kosong</h2>
                            <p>Belum ada produk di keranjang Anda</p>
                        </div>
                    @endif

                    @foreach($normalized as $index => $it)
                        <div class="cart-item">
                            <div class="item-checkbox">
                                <input type="checkbox" name="items[{{ $index }}][selected]" class="item-select" data-index="{{ $index }}" onchange="updateSummaryFromInputs()" checked>
                            </div>

                            <img src="{{ $it['image'] ?? 'https://via.placeholder.com/120' }}" alt="{{ $it['name'] }}" class="item-image">

                            <div class="item-details">
                                <div class="item-name">{{ $it['name'] }}</div>
                                <div class="item-price">{{ number_format($it['price'],0,',','.') }}</div>

                                <div class="quantity-controls">
                                    <button type="button" onclick="decreaseQuantity({{ $index }})">−</button>
                                    <input type="number" name="items[{{ $index }}][quantity]" value="{{ $it['quantity'] }}" min="1" id="qty-{{ $index }}" onchange="updateSummaryFromInputs()">
                                    <button type="button" onclick="increaseQuantity({{ $index }})">+</button>
                                </div>
                            </div>

                            <div class="item-actions">
                                <div class="item-subtotal">Rp <span id="line-total-{{ $index }}" data-unit-price="{{ $it['price'] }}">{{ number_format($it['price'] * $it['quantity'],0,',','.') }}</span></div>
                                <div style="display:flex; gap:8px;">
                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $it['id'] }}">
                                    <input type="hidden" name="items[{{ $index }}][name]" value="{{ $it['name'] }}">
                                    <input type="hidden" name="items[{{ $index }}][price]" value="{{ $it['price'] }}">
                                    <!-- Replace nested form with a button that asks for confirmation and posts via fetch -->
                                    <button type="button" class="btn-remove" onclick="confirmRemove({{ $it['id'] }}, {{ $index }})">🗑️ Hapus</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="cart-summary">
                    <div class="summary-title">Ringkasan Belanja</div>
                    <div class="summary-box">
                        <div class="selected-items-info" id="selectedInfo">0 dari 0 produk dipilih</div>
                        <div class="summary-row"><span>Subtotal:</span><span id="subtotalAmount">Rp 0</span></div>
                        <div class="summary-row total"><span>Total:</span><span id="totalAmount">Rp 0</span></div>
                    </div>
                    <div class="checkout-meta">
                        <input type="hidden" name="preview" value="1">
                        <button class="checkout-btn" type="submit" id="checkoutBtn">Checkout</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // client-side helpers to keep UX (increase/decrease, recalc totals)
        function formatRupiah(amount){ return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',minimumFractionDigits:0}).format(amount); }

        function updateSummaryFromInputs(){
            const rows = document.querySelectorAll('.cart-item');
            let subtotal = 0; let totalCount = 0; let selectedCount = 0;
            rows.forEach((row, idx) => {
                const checkbox = row.querySelector('.item-select');
                const qtyEl = row.querySelector(`#qty-${idx}`);
                const qty = qtyEl ? parseInt(qtyEl.value || '1') : 1;
                // read unit price from data attribute set on the line total element
                const lineSpan = document.getElementById('line-total-' + idx);
                const unit = lineSpan ? Number(lineSpan.getAttribute('data-unit-price')) || 0 : 0;
                const lineTotal = unit * qty;
                if(lineSpan) lineSpan.textContent = new Intl.NumberFormat('id-ID').format(lineTotal);
                if(checkbox && checkbox.checked){ subtotal += lineTotal; selectedCount++; }
                totalCount++;
            });
            const grand = subtotal; // tax removed as requested
            document.getElementById('subtotalAmount').textContent = formatRupiah(subtotal);
            document.getElementById('totalAmount').textContent = formatRupiah(grand);
            document.getElementById('selectedInfo').textContent = `${selectedCount} dari ${totalCount} produk dipilih`;
            const checkoutBtn = document.getElementById('checkoutBtn'); if(checkoutBtn) checkoutBtn.disabled = selectedCount === 0;
            if(checkoutBtn) checkoutBtn.textContent = `Checkout (${selectedCount} Produk)`;
            // update master "selectAll" checkbox state
            const master = document.getElementById('selectAll');
            if(master){
                // Do not use indeterminate state; keep the box empty unless all items are selected
                master.indeterminate = false;
                master.checked = (selectedCount === totalCount && totalCount > 0);
            }
        }

        function increaseQuantity(index){ const el = document.getElementById('qty-' + index); if(el){ el.value = parseInt(el.value || '1') + 1; updateSummaryFromInputs(); }}
        function decreaseQuantity(index){ const el = document.getElementById('qty-' + index); if(el && parseInt(el.value || '1') > 1){ el.value = parseInt(el.value) - 1; updateSummaryFromInputs(); }}

    function toggleSelectAll(){ const master = document.getElementById('selectAll'); document.querySelectorAll('.item-select').forEach(cb=>cb.checked = master.checked); master.indeterminate = false; updateSummaryFromInputs(); }

        // init
        document.addEventListener('DOMContentLoaded', function(){ updateSummaryFromInputs(); });

        // Remove with confirmation using fetch to avoid nested form issues
        function getCsrfToken(){ const el = document.querySelector('meta[name="csrf-token"]'); return el ? el.getAttribute('content') : document.querySelector('input[name="_token"]').value; }

        function confirmRemove(productId, index){
            if(!confirm('Hapus produk ini dari keranjang?')) return;
            postRemove(productId, index);
        }

        async function postRemove(productId, index){
            try{
                const token = getCsrfToken();
                const resp = await fetch(`/cart/remove/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ _token: token })
                });

                if(!resp.ok){
                    const text = await resp.text();
                    alert('Gagal menghapus item: ' + resp.status + ' ' + text);
                    return;
                }

                // Reload the page so server-rendered header/cart count and empty-state are correct
                location.reload();

            }catch(err){
                console.error(err);
                alert('Terjadi kesalahan saat menghapus item.');
            }
        }
    </script>

@endsection
