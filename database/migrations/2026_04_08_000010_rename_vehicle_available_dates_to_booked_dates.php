<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vehicles')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'booked_dates')) {
                $table->json('booked_dates')->nullable()->after('lib_availability_status_id');
            }
        });

        if (Schema::hasColumn('vehicles', 'available_dates')) {
            DB::table('vehicles')->whereNull('booked_dates')->update([
                'booked_dates' => DB::raw('available_dates'),
            ]);

            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('available_dates');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('vehicles')) {
            return;
        }

        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'available_dates')) {
                $table->json('available_dates')->nullable()->after('lib_availability_status_id');
            }
        });

        if (Schema::hasColumn('vehicles', 'booked_dates')) {
            DB::table('vehicles')->whereNull('available_dates')->update([
                'available_dates' => DB::raw('booked_dates'),
            ]);

            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn('booked_dates');
            });
        }
    }
};

