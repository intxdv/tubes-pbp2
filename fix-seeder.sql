-- Reset products dan insert ulang dengan nama file yang benar
TRUNCATE TABLE products;

-- Seeder sudah diperbaiki, jalankan:
-- php artisan db:seed --class=ProductsTableSeeder

-- Atau manual update produk yang error:
UPDATE products SET image = 'setrika.jpg' WHERE image = 'public/images/setrika.jpg';
UPDATE products SET image = 'sepada listrik.jpg' WHERE name = 'Sepeda Listrik';
UPDATE products SET image = 'NATURAL DEODORANT TAWAS .webp' WHERE name = 'NATURAL DEODORANT TAWAS';
UPDATE products SET image = 'Minecraft Java & Bedrock Original Redeem Key Game PCl.webp' WHERE name LIKE '%Minecraft%';
