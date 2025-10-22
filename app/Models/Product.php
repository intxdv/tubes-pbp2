<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'stock',
        'image',
        'category_id',
    ];

    // Accessor untuk URL gambar
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('images/' . $this->image);
        }
        return asset('images/product-default.png');
    }

    // Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    // Relasi ke user (penjual)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Relasi ke order item
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    // Relasi ke review
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
