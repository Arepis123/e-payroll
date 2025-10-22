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
        Schema::table('payroll_workers', function (Blueprint $table) {
            $table->decimal('advance_payment', 10, 2)->default(0)->after('gross_salary');
            $table->text('advance_payment_remarks')->nullable()->after('advance_payment');
            $table->decimal('deduction', 10, 2)->default(0)->after('advance_payment_remarks');
            $table->text('deduction_remarks')->nullable()->after('deduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_workers', function (Blueprint $table) {
            $table->dropColumn(['advance_payment', 'advance_payment_remarks', 'deduction', 'deduction_remarks']);
        });
    }
};
