<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('is_aaracc');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->index(['lib_type_id', 'lib_availability_status_id']);
            $table->index('user_id');
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->index(['vehicle_id', 'status', 'datetime_to']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_aaracc']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['lib_type_id', 'lib_availability_status_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->dropIndex(['vehicle_id', 'status', 'datetime_to']);
        });
    }
};

