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
        Schema::create('payroll_worker_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_worker_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['advance_payment', 'deduction']); // Transaction type
            $table->decimal('amount', 10, 2); // Transaction amount
            $table->text('remarks'); // Mandatory remarks for each transaction
            $table->timestamps();

            // Index for faster queries
            $table->index(['payroll_worker_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_worker_transactions');
    }
};
