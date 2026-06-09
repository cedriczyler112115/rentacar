<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lib_municipality_type_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lib_municipality_id')
                ->constrained('lib_municipalities')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('lib_type_id')
                ->constrained('lib_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['lib_municipality_id', 'lib_type_id'], 'municipality_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lib_municipality_type_prices');
    }
};

