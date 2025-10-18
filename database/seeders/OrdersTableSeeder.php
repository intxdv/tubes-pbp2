<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;

class OrdersTableSeeder extends Seeder
{
    public function run()
    {
        // Intentionally left empty: orders will be created by user actions in the app.
        // This seeder will not create or delete order-related data.
    }
}
