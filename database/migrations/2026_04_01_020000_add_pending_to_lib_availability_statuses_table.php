<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lib_availability_statuses')) {
            return;
        }

        $exists = DB::table('lib_availability_statuses')
            ->whereRaw('LOWER(name) = ?', ['pending'])
            ->exists();

        if (!$exists) {
            DB::table('lib_availability_statuses')->insert([
                'name' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('lib_availability_statuses')) {
            return;
        }

        DB::table('lib_availability_statuses')
            ->whereRaw('LOWER(name) = ?', ['pending'])
            ->delete();
    }
};

