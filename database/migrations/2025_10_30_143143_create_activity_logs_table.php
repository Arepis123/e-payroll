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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // User Information
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('contractor_clab_no')->nullable();
            $table->string('user_name')->nullable(); // Store name for historical record
            $table->string('user_email')->nullable(); // Store email for historical record

            // Activity Details
            $table->string('module'); // e.g., 'payment', 'timesheet', 'worker', 'invoice'
            $table->string('action'); // e.g., 'created', 'updated', 'deleted', 'viewed', 'submitted', 'paid'
            $table->text('description'); // Human-readable description

            // Subject (what was acted upon) - Polymorphic relation
            $table->string('subject_type')->nullable(); // Model class name
            $table->unsignedBigInteger('subject_id')->nullable(); // Model ID

            // Changes tracking
            $table->json('old_values')->nullable(); // Previous state
            $table->json('new_values')->nullable(); // New state

            // Additional metadata
            $table->json('properties')->nullable(); // Any additional context

            // Request information
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable(); // GET, POST, PUT, DELETE

            $table->timestamps();

            // Indexes for faster queries
            $table->index('user_id');
            $table->index('contractor_clab_no');
            $table->index('module');
            $table->index('action');
            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
