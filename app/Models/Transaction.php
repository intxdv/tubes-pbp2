<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Transaction extends Model
{
    protected $fillable = [
        'order_id', 'status', 'payment_method', 'paid_at',
    ];
    public function order() { return $this->belongsTo(Order::class); }
}
