<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transactions') && ! Schema::hasColumn('transactions', 'paid_at')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->timestamp('paid_at')->nullable()->after('payment_method');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'paid_at')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('paid_at');
            });
        }
    }
};
