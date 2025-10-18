<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model
{
    protected $fillable = ['name'];

    public function products() {
        return $this->hasMany(Product::class);
    }

    // Hapus kategori dan set category_id produk menjadi null
    public function deleteWithProducts()
    {
        foreach ($this->products as $product) {
            $product->category_id = null;
            $product->save();
        }
        $this->delete();
    }
}
