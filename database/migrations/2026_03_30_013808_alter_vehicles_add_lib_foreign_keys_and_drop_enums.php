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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['type', 'availability_status', 'transmission', 'fuel_type', 'image']);
            
            $table->foreignId('lib_type_id')->nullable()->constrained('lib_types')->onDelete('set null');
            $table->foreignId('lib_availability_status_id')->nullable()->constrained('lib_availability_statuses')->onDelete('set null');
            $table->foreignId('lib_transmission_id')->nullable()->constrained('lib_transmissions')->onDelete('set null');
            $table->foreignId('lib_fuel_type_id')->nullable()->constrained('lib_fuel_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['lib_type_id']);
            $table->dropForeign(['lib_availability_status_id']);
            $table->dropForeign(['lib_transmission_id']);
            $table->dropForeign(['lib_fuel_type_id']);
            
            $table->dropColumn(['lib_type_id', 'lib_availability_status_id', 'lib_transmission_id', 'lib_fuel_type_id']);
            
            $table->string('type')->nullable();
            $table->enum('availability_status', ['available', 'rented', 'maintenance'])->default('available');
            $table->enum('transmission', ['automatic', 'manual'])->nullable();
            $table->enum('fuel_type', ['gasoline', 'diesel', 'electric'])->nullable();
            $table->string('image')->nullable();
        });
    }
};
