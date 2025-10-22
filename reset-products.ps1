# Script untuk reset dan seed ulang data produk
Write-Host "🔄 Menghapus semua data produk..." -ForegroundColor Yellow
php artisan tinker --execute="App\Models\Product::truncate();"

Write-Host "✅ Data produk berhasil dihapus!" -ForegroundColor Green
Write-Host ""
Write-Host "📦 Menjalankan seeder produk..." -ForegroundColor Yellow
php artisan db:seed --class=ProductsTableSeeder

Write-Host ""
Write-Host "🎉 Selesai! Semua produk sudah di-reset dan di-seed ulang." -ForegroundColor Green
Write-Host "Gambar akan otomatis tampil jika file ada di public/images/" -ForegroundColor Cyan
