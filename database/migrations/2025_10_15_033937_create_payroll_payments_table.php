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
        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_submission_id')->constrained()->onDelete('cascade');
            $table->string('payment_method')->default('billplz'); // billplz
            $table->string('billplz_bill_id')->nullable(); // Billplz bill ID
            $table->string('billplz_url')->nullable(); // Billplz payment URL
            $table->string('transaction_id')->nullable(); // Transaction reference
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->text('payment_response')->nullable(); // JSON response from Billplz
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Index for faster queries
            $table->index('billplz_bill_id');
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_payments');
    }
};
