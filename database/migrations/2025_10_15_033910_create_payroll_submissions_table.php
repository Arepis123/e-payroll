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
        Schema::create('payroll_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('contractor_clab_no'); // Contractor CLAB number
            $table->integer('month'); // 1-12
            $table->integer('year'); // 2025, 2026, etc.
            $table->date('payment_deadline'); // Last day of the month
            $table->enum('status', ['draft', 'pending_payment', 'paid', 'overdue'])->default('draft');
            $table->boolean('has_penalty')->default(false);
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0); // Total amount to pay
            $table->decimal('total_with_penalty', 10, 2)->default(0); // Total + penalty if applicable
            $table->integer('total_workers')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Unique constraint: one submission per contractor per month/year
            $table->unique(['contractor_clab_no', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_submissions');
    }
};
