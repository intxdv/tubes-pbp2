@extends('layouts.app')

@section('content')
@php
    // Normalize $items to a consistent array shape to avoid "property on array" errors
    $normalized = [];
    foreach (($items ?? []) as $it) {
        // handle array shapes
        if (is_array($it)) {
            $prod = $it['product'] ?? null;
            if (is_array($prod)) {
                $prodId = $it['product_id'] ?? ($prod['id'] ?? ($it['id'] ?? null));
                $name = $it['name'] ?? ($prod['name'] ?? 'Item');
            } else {
                $prodId = $it['product_id'] ?? ($it['id'] ?? null);
                $name = $it['name'] ?? ($it['title'] ?? 'Item');
            }
            $price = $it['price'] ?? 0;
            $qty = $it['quantity'] ?? 1;
        } else {
            // object-like: may have ->product as object or array; guard both
            $prod = isset($it->product) ? $it->product : (is_array($it->product ?? null) ? (object) ($it->product ?? []) : null);
            $prodId = $it->product_id ?? ($prod->id ?? ($it->id ?? null));
            $name = $prod->name ?? ($it->name ?? 'Item');
            $price = $it->price ?? 0;
            $qty = $it->quantity ?? ($it->qty ?? 1);
        }
        $normalized[] = [
            'product_id' => $prodId,
            'name' => $name,
            'price' => $price,
            'quantity' => $qty,
        ];
    }
    $items = $normalized;
@endphp

<div style="max-width:900px; margin:30px auto;">
    <div style="background:#fff; border-radius:6px; box-shadow:0 6px 18px rgba(0,0,0,0.06); padding:28px;">
        <div style="display:flex; gap:28px; align-items:flex-start;">
                <div style="flex:1; min-width:0;">
                <div style="margin-bottom:12px;"><a href="/" style="color:#2563eb; font-weight:700; text-decoration:none;">🏠 Beranda</a></div>
                @if(request()->query('return_to'))
                    <div style="margin-bottom:8px;"><a href="{{ request()->query('return_to') }}" style="color:#374151; text-decoration:none; font-weight:600;">&larr; Kembali ke produk</a></div>
                @endif
                <h2 style="font-size:22px; font-weight:700; margin:0 0 18px 0;">Checkout</h2>

                <form id="checkout-form" method="POST" action="/cart/checkout">
                    @csrf

                    <div style="margin-bottom:14px;">
                        <label style="display:block; font-size:13px; color:#333; margin-bottom:6px;">Pilih Alamat</label>
                        <select id="address_select" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                            <option value="">-- Tambah alamat baru --</option>
                            @foreach((Auth::user() && method_exists(Auth::user(), 'addresses') ? Auth::user()->addresses : []) as $addr)
                                <option value="{{ $addr->id }}">{{ $addr->label ?? 'Alamat' }} — {{ strlen($addr->address) > 60 ? substr($addr->address,0,57).'...' : $addr->address }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="new-address-form" style="display:block; padding:12px; border:1px solid #eee; border-radius:6px; margin-bottom:12px;">
                        <div style="margin-bottom:10px;">
                            <label style="display:block; font-size:13px; color:#333; margin-bottom:6px;">Nama penerima</label>
                            <input type="text" name="recipient_name" id="recipient_name" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                        </div>
                        <div style="margin-bottom:10px;">
                            <label style="display:block; font-size:13px; color:#333; margin-bottom:6px;">Nomor telepon</label>
                            <input type="text" name="phone" id="phone" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;">
                        </div>
                        <div>
                            <label style="display:block; font-size:13px; color:#333; margin-bottom:6px;">Alamat lengkap</label>
                            <textarea name="address" id="address" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:4px;" rows="3"></textarea>
                        </div>
                    </div>

                    <div style="margin-bottom:12px;">
                        <label style="display:block; font-size:13px; color:#333; margin-bottom:6px;">Metode Pembayaran</label>
                        <select id="payment_method" name="payment_method" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px;">
                            <option value="transfer">Transfer Bank</option>
                            <option value="cod">Cash on Delivery</option>
                        </select>
                    </div>



                    <div>
                        <button id="pay-button" type="submit" style="background:linear-gradient(180deg,#f59e0b,#f97316); border:none; color:white; padding:12px 20px; border-radius:6px; font-weight:700; cursor:pointer;">Bayar Sekarang</button>
                    </div>
                </form>
            </div>

            <div style="width:320px;">
                <div style="border:1px solid #eee; padding:14px; border-radius:6px; background:#fafafa;">
                    <h3 style="margin:0 0 10px 0; font-weight:700;">Ringkasan</h3>
                    <div style="margin-bottom:6px;">
                        @php $total = 0; @endphp
                        @forelse($items as $item)
                            @php
                                $dataItemId = isset($item['product_id']) ? $item['product_id'] : (isset($item['id']) ? $item['id'] : null);
                                $unitPrice = $item['price'] ?? 0;
                                $qty = $item['quantity'] ?? 1;
                            @endphp
                            <div style="display:flex; justify-content:space-between; margin-bottom:8px; align-items:center;">
                                <div style="font-size:14px;">{{ $item['name'] }}</div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="min-width:110px; text-align:right;">Rp <span class="item-total" data-item-id="{{ 'p'.($dataItemId ?? '') }}" data-unit-price="{{ $unitPrice }}">{{ number_format($unitPrice * $qty, 0, ',', '.') }}</span></div>
                                </div>
                            </div>
                            @php $total += ($unitPrice * $qty); @endphp
                        @empty
                            <div>Keranjang kosong.</div>
                        @endforelse
                    </div>
                    <hr style="border:none; border-top:1px solid #ececec; margin:10px 0;">
                    <div style="display:flex; justify-content:space-between; font-weight:700; font-size:15px;">Total <div>Rp <span id="grand-total">{{ number_format($total, 0, ',', '.') }}</span></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- simple spinner element (hidden) -->
<div id="checkout-spinner" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.25); align-items:center; justify-content:center; z-index:9999;">
    <div style="background:white; padding:20px 26px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.08); font-weight:700;">Processing...</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const paymentSelect = document.getElementById('payment_method');
    const addressSelect = document.getElementById('address_select');
    const newAddressForm = document.getElementById('new-address-form');

    // Address selection toggles new-address form
    addressSelect.addEventListener('change', function(){
        if (this.value) {
            newAddressForm.style.display = 'none';
        } else {
            newAddressForm.style.display = 'block';
        }
    });

    // Quantity display / total calc (read-only in checkout)
    function recalcTotals() {
        let grand = 0;
        document.querySelectorAll('.item-total').forEach(function(span){
            const unit = parseInt(span.getAttribute('data-unit-price')) || 0;
            // find corresponding qty display
            const id = span.getAttribute('data-item-id');
            const qtyEl = document.querySelector('.qty-display[data-item-id="'+id+'"]');
            const qty = qtyEl ? parseInt(qtyEl.textContent || '1') : 1;
            const newTotal = unit * qty;
            span.textContent = new Intl.NumberFormat('id-ID').format(newTotal);
            grand += newTotal;
        });
        document.getElementById('grand-total').textContent = new Intl.NumberFormat('id-ID').format(grand);
    }

    recalcTotals();

    const form = document.getElementById('checkout-form');
    const payButton = document.getElementById('pay-button');
    const spinner = document.getElementById('checkout-spinner');
    form.addEventListener('submit', function(e){
        // show a simple spinner and allow the form to submit normally so the
        // server-side checkout handler can finalize and redirect.
        payButton.disabled = true;
        spinner.style.display = 'flex';
        return true; // allow normal submit
    });
});
</script>

@endsection