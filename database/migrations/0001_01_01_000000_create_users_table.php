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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique(); // From u_username
            $table->string('contractor_clab_no')->nullable()->index(); // From u_username, used to link with worker contracts
            $table->string('name'); // From u_fname (contractor name)
            $table->string('email')->unique(); // From u_email
            $table->string('phone')->nullable(); // From u_contactno
            $table->string('person_in_charge')->nullable(); // From u_lname
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // Nullable because auth is from third-party DB
            $table->string('role')->default('client'); // Role management
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
