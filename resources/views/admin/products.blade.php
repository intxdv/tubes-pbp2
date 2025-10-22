@extends('admin.layout')

@section('title', 'Manajemen Produk')

@section('content')
<!-- Notification -->
<div id="notification" class="hidden fixed top-4 right-4 px-4 py-2 rounded shadow-lg" role="alert">
    <span id="notification-message"></span>
</div>

<h1 class="page-title">Manajemen Produk</h1>

<!-- Kategori Produk -->
<div class="section">
    <div class="table-header">
        <h2 class="section-title" style="margin-bottom: 0;">Kategori Produk</h2>
        <button class="btn-primary" onclick="showAddCategoryModal()">+ Tambah Kategori</button>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Kategori</th>
                <th>Jumlah Produk</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->products_count }}</td>
                <td>
                    <form action="{{ route('admin.categories.delete', $category->id) }}" method="POST" style="display: inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- CRUD Produk -->
<div class="section">
    <div class="table-header">
        <h2 class="section-title" style="margin-bottom: 0;">Daftar Produk</h2>
        <button class="btn-primary" onclick="showAddProductModal()">+ Tambah Produk</button>
    </div>
    <table class="w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="px-4 py-2 border-b">Gambar</th>
                <th class="px-4 py-2 border-b">Nama Produk</th>
                <th class="px-4 py-2 border-b">Kategori</th>
                <th class="px-4 py-2 border-b">Harga</th>
                <th class="px-4 py-2 border-b">Stok</th>
                <th class="px-4 py-2 border-b">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 border-b">
                    @if($product->image)
                        <div class="w-16 h-16">
                            <img src="{{ asset('images/' . rawurlencode($product->image)) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover rounded">
                        </div>
                    @endif
                </td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td>Rp {{ number_format($product->price) }}</td>
                <td>{{ $product->stock }}</td>
                <td>
                    <button class="btn-action btn-edit" onclick="showEditProductModal({{ $product->id }})">Edit</button>
                    <button class="btn-action btn-delete" onclick="confirmDelete({{ $product->id }})">Hapus</button>
                    <form id="delete-form-{{ $product->id }}" action="{{ route('admin.products.delete', $product->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Add Category -->
<div id="addCategoryModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Tambah Kategori</h3>
        <form action="{{ route('admin.categories.add') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="category-name">Nama Kategori</label>
                <input type="text" id="category-name" name="name" required>
            </div>
            <div class="form-actions">
                <button type="button" onclick="hideAddCategoryModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add/Edit Product -->
<div id="productModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3 id="productModalTitle">Tambah Produk</h3>
        <form id="productForm" action="{{ route('admin.products.add') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" value="POST">
            
            <div class="form-group">
                <label for="product-name">Nama Produk</label>
                <input type="text" id="product-name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="product-category">Kategori</label>
                <select id="product-category" name="category_id" required>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label for="product-price">Harga</label>
                <input type="number" id="product-price" name="price" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="product-stock">Stok</label>
                <input type="number" id="product-stock" name="stock" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="product-image">Gambar (Opsional)</label>
                <input type="file" id="product-image" name="image" accept="image/*" onchange="previewImage(this)">
                <div class="mt-2">
                    <img id="preview-image" src="#" alt="Preview" style="display: none;" class="w-32 h-32 object-cover rounded">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" onclick="hideProductModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<style>
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
    max-width: 500px;
}

.modal-content h3 {
    margin-bottom: 20px;
    color: #2c3e50;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #4b5563;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #2c3e50;
    box-shadow: 0 0 0 2px rgba(44,62,80,0.1);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 30px;
}

.btn-secondary {
    padding: 10px 20px;
    background-color: #e5e7eb;
    color: #4b5563;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background-color: #d1d5db;
}
</style>

@endsection

@push('scripts')
<script>
// Simple notification helpers
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    const messageElement = document.getElementById('notification-message');
    notification.className = 'alert alert-' + type;
    messageElement.textContent = message;
    notification.style.display = 'block';
    if (type !== 'info') {
        setTimeout(() => notification.style.display = 'none', 3000);
    }
}

function hideNotification() {
    const notification = document.getElementById('notification');
    notification.style.display = 'none';
}

function showAddCategoryModal() {
    document.getElementById('addCategoryModal').style.display = 'flex';
}
function hideAddCategoryModal() {
    document.getElementById('addCategoryModal').style.display = 'none';
}

function showAddProductModal() {
    const form = document.getElementById('productForm');
    form.reset();
    form.action = "{{ route('admin.products.add') }}";
    form.querySelector('input[name="_method"]').value = 'POST';
    document.getElementById('productModalTitle').textContent = 'Tambah Produk';
    const preview = document.getElementById('preview-image');
    preview.style.display = 'none';
    document.getElementById('productModal').style.display = 'flex';
}

function showEditProductModal(productId) {
    const form = document.getElementById('productForm');
    form.action = `/admin/products/${productId}`;
    form.querySelector('input[name="_method"]').value = 'PUT';
    document.getElementById('productModalTitle').textContent = 'Edit Produk';
    document.getElementById('productModal').style.display = 'flex';
    showNotification('Loading product data...', 'info');

    fetch(`/admin/products/${productId}`)
        .then(response => response.json())
        .then(product => {
            hideNotification();
            document.getElementById('product-name').value = product.name || '';
            document.getElementById('product-category').value = product.category_id || '';
            document.getElementById('product-price').value = product.price || '';
            document.getElementById('product-stock').value = product.stock || 0;
            const preview = document.getElementById('preview-image');
            if (product.image) {
                preview.src = '/images/' + product.image;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        })
        .catch(err => {
            hideNotification();
            showNotification('Gagal mengambil data produk', 'error');
        });
}

function hideProductModal() {
    document.getElementById('productModal').style.display = 'none';
}

function confirmDelete(productId) {
    if (confirm('Yakin ingin menghapus produk ini?')) {
        document.getElementById(`delete-form-${productId}`).submit();
    }
}

function previewImage(input) {
    const previewImg = document.getElementById('preview-image');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        previewImg.style.display = 'none';
    }
}

// Attach non-AJAX submit behavior: forms submit normally to controller endpoints
document.addEventListener('DOMContentLoaded', function() {
    // Nothing special required; productForm will post multipart/form-data to controller
});
</script>
@endpush