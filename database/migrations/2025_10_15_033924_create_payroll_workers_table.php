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
        Schema::create('payroll_workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_submission_id')->constrained()->onDelete('cascade');
            $table->string('worker_id'); // wkr_id from workers table
            $table->string('worker_name');
            $table->string('worker_passport');
            $table->decimal('basic_salary', 10, 2);

            // Work hours and overtime
            $table->decimal('regular_hours', 8, 2)->default(0); // Regular working hours
            $table->decimal('ot_normal_hours', 8, 2)->default(0); // Overtime normal day (1.5x)
            $table->decimal('ot_rest_hours', 8, 2)->default(0); // Overtime rest day (2x)
            $table->decimal('ot_public_hours', 8, 2)->default(0); // Overtime public holiday (3x)

            // Calculated amounts
            $table->decimal('regular_pay', 10, 2)->default(0);
            $table->decimal('ot_normal_pay', 10, 2)->default(0);
            $table->decimal('ot_rest_pay', 10, 2)->default(0);
            $table->decimal('ot_public_pay', 10, 2)->default(0);
            $table->decimal('total_ot_pay', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2)->default(0); // Basic + OT

            // Deductions
            $table->decimal('epf_employee', 10, 2)->default(0); // 2%
            $table->decimal('socso_employee', 10, 2)->default(0); // 0.5%
            $table->decimal('total_deductions', 10, 2)->default(0);

            // Employer contributions
            $table->decimal('epf_employer', 10, 2)->default(0); // 2%
            $table->decimal('socso_employer', 10, 2)->default(0); // 1.75%
            $table->decimal('total_employer_contribution', 10, 2)->default(0);

            // Final amounts
            $table->decimal('net_salary', 10, 2)->default(0); // Gross - Deductions
            $table->decimal('total_payment', 10, 2)->default(0); // Net + Employer Contributions

            $table->timestamps();

            // Index for faster queries
            $table->index(['payroll_submission_id', 'worker_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_workers');
    }
};
