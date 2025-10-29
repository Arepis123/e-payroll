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
        Schema::create('salary_adjustments', function (Blueprint $table) {
            $table->id();
            $table->integer('worker_id')->index(); // Worker ID from second database
            $table->string('worker_name'); // Worker name (for history reference)
            $table->string('worker_passport')->nullable(); // Passport number
            $table->decimal('old_salary', 10, 2); // Previous salary
            $table->decimal('new_salary', 10, 2); // New salary
            $table->foreignId('adjusted_by')->constrained('users')->onDelete('cascade'); // Super admin who made the change
            $table->text('remarks')->nullable(); // Optional notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_adjustments');
    }
};
