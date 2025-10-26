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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Template name (e.g., "Payment Reminder")
            $table->string('slug')->unique(); // Unique identifier (e.g., "payment_reminder")
            $table->string('type'); // email, sms, system
            $table->enum('trigger_type', ['manual', 'auto_payment_deadline', 'auto_overdue', 'auto_submission_deadline', 'auto_document_expiry'])->default('manual');
            $table->integer('trigger_days_before')->nullable(); // Days before event to trigger (for auto reminders)
            $table->string('subject')->nullable(); // Email subject
            $table->text('body'); // Email body / message content
            $table->text('variables')->nullable(); // JSON array of available variables
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
