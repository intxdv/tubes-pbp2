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
            // Elektronik & Gadget
            ['Setrika', 'Setrika listrik hemat energi dengan pelat anti lengket.', 250000, 25, 'setrika.jpg', 1],
            ['PC Gaming', 'PC gaming dengan prosesor Intel i7 dan kartu grafis RTX 4060.', 18500000, 8, 'pc gaming.jpg', 1],
            ['Oven Mito', 'Oven listrik multifungsi kapasitas 20 liter.', 850000, 20, 'oven mito.jpg', 1],
            ['MSI CLAW 8', 'Konsol gaming portabel MSI Claw generasi terbaru.', 16500000, 5, 'MSI CLAW 8 .jpg', 1],
            ['Monitor Lenovo', 'Monitor Lenovo 27 inci full HD dengan refresh rate 75Hz.', 2100000, 12, 'monitor lenovo.jpg', 1],
            ['Laptop ROG', 'Laptop gaming ASUS ROG dengan performa tinggi untuk gamer profesional.', 23500000, 6, 'laptop ROG.jpeg', 1],

            //Otomotif
            ['Sepeda Listrik', 'Sepeda listrik ramah lingkungan dengan baterai tahan lama.', 8500000, 10, 'sepada listrik.jpg', 7],
            ['Motor Vespa', 'Motor vespa klasik dengan desain elegan dan mesin halus.', 25000000, 5, 'motor vespa.jpg', 7],
            ['Mobil F1 Miniatur', 'Miniatur mobil F1 dengan detail realistis untuk koleksi.', 450000, 20, 'mobil f1.jpg', 7],
            ['Helm Full Face Sport', 'Helm full face dengan ventilasi udara dan kaca anti gores.', 750000, 15, 'helm2.jpeg', 7],
            ['Helm Half Face Retro', 'Helm half face bergaya retro dengan desain klasik.', 500000, 18, 'helm1.jpeg', 7],
            ['Dash Cam HD', 'Kamera mobil dengan resolusi tinggi untuk merekam perjalanan.', 600000, 25, 'dash cam.jpg', 7],
            ['Aki Mobil 12V', 'Aki mobil berkualitas tinggi dengan daya tahan lama.', 1200000, 12, 'aki mobil.jpg', 7],

            // Fashion & Aksesoris
            ['Watch Silver', 'Jam tangan silver elegan dengan tali stainless steel.', 350000, 15, 'watch silver.jpeg', 2],
            ['Watch Gold', 'Jam tangan gold dengan desain mewah dan tahan air.', 420000, 12, 'watch gold.jpeg', 2],
            ['Shirt White Sun', 'Kemeja putih polos bahan katun nyaman dipakai.', 180000, 20, 'shirt white sun.jpeg', 2],
            ['Shirt White About Black', 'Kemeja putih dengan tulisan hitam kasual.', 190000, 25, 'shirt white about;black.jpeg', 2],
            ['Shirt Blue', 'Kemeja biru slim fit cocok untuk acara formal.', 175000, 18, 'shirt blue.jpeg', 2],
            ['Jaket Jeans', 'Jaket jeans klasik dengan bahan tebal dan kuat.', 350000, 15, 'jaket jeans.jpeg', 2],
            ['Hoodie Zip', 'Hoodie dengan resleting depan, cocok untuk cuaca dingin.', 250000, 20, 'hoodie zip.jpeg', 2],
            ['Hoodie Biru', 'Hoodie biru polos berbahan lembut dan nyaman.', 230000, 22, 'hoodie biru.jpeg', 2],

            // Kecantikan & Perawatan
            ['WARDAH Matte Lip Cream', 'Lip cream matte tahan lama dengan warna lembut.', 78000, 40, 'WARDAH Matte Lip Cream.webp', 3],
            ['Viva White Hand Serum + AntiBacterial', 'Serum tangan dengan perlindungan antibakteri.', 35000, 50, 'Viva White Hand Serum + AntiBacterial.png', 3],
            ['Parfum Elegant', 'Parfum wangi lembut dengan aroma elegan.', 120000, 25, 'parfum1.jpeg', 3],
            ['NATURAL DEODORANT TAWAS', 'Deodoran alami tanpa alkohol, aman untuk kulit sensitif.', 42000, 30, 'NATURAL DEODORANT TAWAS .webp', 3],
            ['Makarizo Hair Energy Fibertherapy', 'Perawatan rambut rusak dengan aroma segar.', 65000, 35, 'Makarizo Hair Energy Fibertherapy.webp', 3],
            ['GARNIER Micellar Water', 'Pembersih wajah lembut untuk semua jenis kulit.', 65000, 45, 'GARNIER Micellar Water.webp', 3],
            
            // Rumah & Taman
            ['Tempat Tidur', 'Tempat tidur kayu minimalis dengan desain modern.', 3500000, 8, 'tempat tidur.jpg', 4],
            ['Tempat Sampah', 'Tempat sampah stainless dengan tutup otomatis.', 250000, 20, 'tempat sampah.jpg', 4],
            ['Meja', 'Meja kerja kayu solid dengan ukuran sedang.', 1200000, 10, 'meja.jpg', 4],
            ['Lampu Taman', 'Lampu taman tenaga surya hemat energi.', 300000, 25, 'lampu taman.jpg', 4],
            ['Kursi', 'Kursi rotan sintetis cocok untuk ruang tamu atau taman.', 450000, 15, 'Kursi.jpg', 4],
            ['Gelas', 'Set gelas kaca bening isi 6 pcs.', 90000, 30, 'gelas.jpg', 4],
            
            // Olahraga & Outdoor
            ['Bola Basket', 'Bola basket berkualitas untuk latihan dan pertandingan.', 250000, 20, 'bola basket.jpg', 5],
            ['Sepatu Sepak Bola Nike', 'Sepatu sepak bola dengan grip kuat untuk semua medan.', 650000, 15, 'footballshoes.jpeg', 5],
            ['Kacamata Renang Anak', 'Kacamata renang anti kabut dan nyaman digunakan.', 120000, 25, 'kacamata renang.jpg', 5],
            ['Raket Badminton', 'Raket badminton ringan untuk permainan cepat dan akurat.', 300000, 30, 'raket badminton.jpg', 5],
            ['Sepatu Lari Nike Adizero', 'Sepatu lari desain ergonomis dan empuk.', 500000, 20, 'runningshoes1.jpeg', 5],

            // Hobi & Mainan
            ['Mainan Robot', 'Mainan robot edukatif untuk anak.', 180000, 25, 'robot.jpg', 6],
            ['Boneka Kayu Jepang', 'Boneka kayu Jepang unik untuk koleksi dan dekorasi.', 180000, 20, 'Boneka Kayu Jepang.jpg', 6],
            ['Buku Novel Keajaiban Toko Kelontong Namiya', 'Novel inspiratif karya Keigo Higashino, kisah penuh makna.', 95000, 15, 'Buku Novel Keajaiban Toko Kelontong Namiya.webp', 6],
            ['Buku Novel Weathering With You', 'Novel adaptasi film terkenal karya Makoto Shinkai.', 90000, 18, 'Buku Novel Weathering With You by Shinkai Makoto.jpg', 6],
            ['Jumping Pirates Roulette Family Game', 'Permainan seru untuk keluarga, siapa yang membuat bajak laut melompat?', 120000, 25, 'Jumping Pirates Roulette Family Game.jpg', 6],
            ['Lego Classic Box', 'Kreativitas tanpa batas dengan set lego klasik.', 350000, 30, 'lego1.jpeg', 6],
            ['Mobil Hotwheels Baja Blazers', 'Hotwheels edisi Baja Blazers Ford Escort RS1600.', 95000, 40, 'Mobil Hotwheels Baja Blazers 70 Ford Escort RS1600.jpg', 6],
            ['NERF N Series AGILITY Blaster N1 Commander', 'Mainan tembak busa untuk aktivitas seru bersama teman.', 275000, 25, 'NERF N Series AGILITY Blaster N1 Commander.jpg', 6],
            ['One Piece Card Game Emporio Ivankov', 'Kartu koleksi One Piece edisi Emporio Ivankov.', 160000, 20, 'One Piece Card Game - Emporio Ivankov.jpg', 6],
            ['Paket 6 Novel Hyouka Series', 'Paket lengkap 6 novel Hyouka karya Honobu Yonezawa.', 420000, 10, 'Paket 6 Novel Hyouka Series 1 2 3 4 5 6.jpg', 6],

            // Makanan & Minuman 
            ['King\'s Fisher Tuna In Hot', 'Tuna kaleng pedas siap saji dengan cita rasa gurih.', 32000, 50, 'King\'s Fisher Tuna In Hot.jpg', 8],
            ['Dark Espresso Robusta 500', 'Kopi robusta hitam pekat dengan aroma kuat.', 75000, 30, 'Dark Espresso Robusta 500.jpg', 8],
            ['CAP KAKI TIGA LARUTAN', 'Minuman larutan penyegar untuk meredakan panas dalam.', 10000, 80, 'CAP KAKI TIGA LARUTAN.jpg', 8],
            ['Bakpia Pathok 25 Premium', 'Bakpia isi kacang hijau khas Yogyakarta.', 55000, 40, 'Bakpia Pathok 25 Premium.jpg', 8],
            ['AVOCADO LATTE Tadi Pagi', 'Minuman latte alpukat premium dengan rasa lembut.', 45000, 35, 'AVOCADO LATTE Tadi Pagi.jpg', 8],
            ['AoNori Rumput Laut Bubuk- Ao', 'Bubuk rumput laut kering untuk topping makanan Jepang.', 60000, 25, 'AoNori Rumput Laut Bubuk- Ao.jpg', 8],
            ['Abon Ikan', 'Abon ikan gurih dan renyah siap saji.', 35000, 45, 'abon ikan.jpg', 8],
            
            // Kesehatan & Obat 
            ['Tolak Angin', 'Obat herbal cair untuk masuk angin dan menjaga daya tahan tubuh.', 15000, 80, 'tolakangin.jpg', 9],
            ['Tempra Paracetamol', 'Obat penurun panas dan pereda nyeri untuk anak.', 25000, 60, 'tempra paracetamol.jpg', 9],
            ['Flimty Obat Diet', 'Suplemen serat alami untuk membantu program diet sehat.', 200000, 30, 'flimty obat diet.jpg', 9],
            ['Counterpain', 'Krim pereda nyeri otot dan sendi dengan efek hangat.', 40000, 45, 'counterpain.jpg', 9],
            ['Betadine', 'Antiseptik cair untuk membersihkan luka ringan.', 18000, 50, 'betadine.jpg', 9],
                    
            // Digital & Layanan
            ['Minecraft Java & Bedrock Original Key', 'Kode redeem resmi Minecraft Java & Bedrock Edition.', 490000, 30, 'Minecraft Java & Bedrock Original Redeem Key Game PCl.webp', 10],
            ['Mobile Legends 3 Diamonds', 'Top-up instan 3 Diamonds untuk game Mobile Legends.', 1500, 500, 'mobile-legends 3 Diamonds.webp', 10],
            ['Mobile Legends 28170 Diamonds', 'Top-up besar 28170 Diamonds Mobile Legends, harga spesial.', 7200000, 10, 'mobile-legends 28170 Diamonds.webp', 10],
            ['Valorant 475 VP', 'Voucher top-up 475 Valorant Points.', 65000, 100, 'valorant 475 VP.webp', 10],
            ['Valorant 22000 VP', 'Paket besar 22000 Valorant Points untuk kebutuhan premium.', 2800000, 10, 'valorant 22000 VP.webp', 10],
            ['Open Trip Komodo Luxury Phinisi 3D2N', 'Paket wisata mewah ke Labuan Bajo selama 3 hari 2 malam.', 4500000, 8, 'Open Trip Komodo Luxury Phinisi 3D2N.jpg', 10],
            ['Ubud Show Kecak Dance', 'Tiket pertunjukan Kecak Dance autentik di Ubud, Bali.', 120000, 50, 'Ubud Show Kecak Dance.jpg', 10],
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
