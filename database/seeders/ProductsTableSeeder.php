<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            1 => 'Elektronik & Gadget',
            2 => 'Fashion & Aksesoris',
            3 => 'Kecantikan & Perawatan',
            4 => 'Rumah & Taman',
            5 => 'Olahraga & Outdoor',
            6 => 'Hobi & Mainan',
            7 => 'Otomotif',
            8 => 'Makanan & Minuman',
            9 => 'Kesehatan & Obat',
            10 => 'Digital & Layanan',
        ];

        $products = [
            ['Laptop Pro 15', 'Laptop high-end untuk kebutuhan profesional.', 15000000, 5, 'laptoppro15.jpg', 1],
            ['Smartphone X', 'Smartphone flagship dengan fitur terbaru.', 8000000, 10, 'smartphonex.jpg', 1],
            ['Jaket Hoodie', 'Jaket hoodie nyaman untuk gaya kasual.', 350000, 20, 'hoodie.jpg', 2],
            ['Lipstik Matte', 'Lipstik matte tahan lama dan warna cerah.', 120000, 30, 'lipstik.jpg', 3],
            ['Set Alat Taman', 'Perlengkapan taman lengkap untuk rumah.', 250000, 15, 'alat_taman.jpg', 4],
            ['Sepatu Lari', 'Sepatu lari ringan dan nyaman.', 500000, 12, 'sepatu_lari.jpg', 5],
            ['Mainan Robot', 'Mainan robot edukatif untuk anak.', 180000, 25, 'robot.jpg', 6],
            ['Oli Motor', 'Oli motor kualitas terbaik.', 90000, 40, 'oli.jpg', 7],
            ['Coklat Premium', 'Coklat premium rasa lezat.', 75000, 50, 'coklat.jpg', 8],
            ['Vitamin C', 'Suplemen vitamin C untuk daya tahan tubuh.', 60000, 60, 'vitamin_c.jpg', 9],
            ['Paket Internet', 'Paket internet bulanan murah.', 100000, 100, 'internet.jpg', 10],
        ];

        foreach ($products as $p) {
            Product::create([
                'name' => $p[0],
                'description' => $p[1],
                'price' => $p[2],
                'stock' => $p[3],
                'image' => $p[4],
                'category_id' => $p[5],
            ]);
        }
    }
}
