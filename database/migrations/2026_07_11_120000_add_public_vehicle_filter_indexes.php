<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->index('price_per_day', 'vehicles_price_per_day_idx');
            $table->index('seating_capacity', 'vehicles_seating_capacity_idx');
            $table->index(['lib_availability_status_id', 'price_per_day'], 'vehicles_status_price_idx');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex('vehicles_status_price_idx');
            $table->dropIndex('vehicles_seating_capacity_idx');
            $table->dropIndex('vehicles_price_per_day_idx');
        });
    }
};
