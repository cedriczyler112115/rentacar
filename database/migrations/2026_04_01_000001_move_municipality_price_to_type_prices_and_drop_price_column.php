<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lib_municipality_type_prices')) {
            return;
        }

        if (Schema::hasColumn('lib_municipalities', 'price')) {
            $typeIds = DB::table('lib_types')->pluck('id')->all();

            if (count($typeIds) > 0) {
                DB::table('lib_municipalities')
                    ->select('id', 'price')
                    ->orderBy('id')
                    ->chunk(200, function ($rows) use ($typeIds) {
                        $inserts = [];
                        foreach ($rows as $row) {
                            foreach ($typeIds as $typeId) {
                                $inserts[] = [
                                    'lib_municipality_id' => $row->id,
                                    'lib_type_id' => $typeId,
                                    'price' => $row->price,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                        DB::table('lib_municipality_type_prices')->insertOrIgnore($inserts);
                    });
            }

            Schema::table('lib_municipalities', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('lib_municipalities', 'price')) {
            Schema::table('lib_municipalities', function (Blueprint $table) {
                $table->decimal('price', 10, 2)->default(0);
            });
        }

        if (Schema::hasTable('lib_municipality_type_prices')) {
            $prices = DB::table('lib_municipality_type_prices')
                ->select('lib_municipality_id', DB::raw('MIN(lib_type_id) as min_type_id'))
                ->groupBy('lib_municipality_id')
                ->get();

            foreach ($prices as $p) {
                $val = DB::table('lib_municipality_type_prices')
                    ->where('lib_municipality_id', $p->lib_municipality_id)
                    ->where('lib_type_id', $p->min_type_id)
                    ->value('price');

                DB::table('lib_municipalities')
                    ->where('id', $p->lib_municipality_id)
                    ->update(['price' => $val ?? 0]);
            }
        }
    }
};

