<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_capitals', function (Blueprint $table) {
            $table->json('capital_additions_log')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('member_capitals', function (Blueprint $table) {
            $table->dropColumn('capital_additions_log');
        });
    }
};

