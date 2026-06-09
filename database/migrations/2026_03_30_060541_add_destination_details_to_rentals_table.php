<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->string('region')->after('vehicle_id');
            $table->string('province')->after('region');
            $table->string('municipality')->after('province');
            $table->decimal('destination_price', 10, 2)->after('municipality');
            $table->boolean('has_carwash')->default(false)->after('destination_price');
            $table->decimal('carwash_fee', 10, 2)->default(0)->after('has_carwash');
            $table->integer('extra_hours')->default(0)->after('carwash_fee');
            $table->decimal('extra_hours_fee', 10, 2)->default(0)->after('extra_hours');
            $table->dropColumn('destination');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->string('destination')->after('vehicle_id');
            $table->dropColumn(['region', 'province', 'municipality', 'destination_price', 'has_carwash', 'carwash_fee', 'extra_hours', 'extra_hours_fee']);
        });
    }
};
