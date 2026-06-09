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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrower_id')->constrained('users')->onDelete('cascade');
            $table->string('borrower_name')->nullable();
            $table->enum('borrower_type', ['member', 'non-member'])->default('non-member');
            $table->boolean('is_aaracc')->default(false);
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->string('interest_type')->default('diminishing');
            $table->integer('term_length_months');
            $table->date('loan_start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('loan_status', ['pending', 'approved', 'active', 'completed', 'overdue', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('date_approved')->nullable();
            $table->timestamps();

            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
