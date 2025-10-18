<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Category;
class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Elektronik & Gadget',
            'Fashion & Aksesoris',
            'Kecantikan & Perawatan',
            'Rumah & Taman',
            'Olahraga & Outdoor',
            'Hobi & Mainan',
            'Otomotif',
            'Makanan & Minuman',
            'Kesehatan & Obat',
            'Digital & Layanan',
        ];
        foreach ($categories as $cat) {
            Category::updateOrCreate(['name' => $cat]);
        }
    }
}
