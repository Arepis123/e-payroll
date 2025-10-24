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
        Schema::create('payroll_reminders', function (Blueprint $table) {
            $table->id();
            $table->string('contractor_clab_no');
            $table->string('contractor_name');
            $table->string('contractor_email');
            $table->integer('month');
            $table->integer('year');
            $table->text('message');
            $table->string('sent_by')->nullable(); // admin user who sent the reminder
            $table->timestamps();

            // Index for faster queries
            $table->index(['contractor_clab_no', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_reminders');
    }
};
