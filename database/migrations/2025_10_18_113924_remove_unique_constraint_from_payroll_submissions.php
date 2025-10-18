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
        Schema::table('payroll_submissions', function (Blueprint $table) {
            // Drop the unique constraint to allow multiple submissions per month
            $table->dropUnique(['contractor_clab_no', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_submissions', function (Blueprint $table) {
            // Restore the unique constraint if rolling back
            $table->unique(['contractor_clab_no', 'month', 'year']);
        });
    }
};
