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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
        });

        

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('email', 100)->unique();
            $table->binary('password', 60);
            $table->enum('type', ['admin', 'client', 'customer'])->default('customer');
            $table->enum('status' , ['pending','active' , 'closed' , 'locked']);
            $table->timestamps();
        });

        Schema::create('account_log', function (Blueprint $table) {
            $table->id();
            $table->json('details'); //reject cause
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('contact_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('value', 20);
            $table->foreignId('user_id')->constrained();
            $table->foreignId('contact_type_id')->constrained();
            $table->timestamps();
        }); 
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->date('experience_start');
            $table->string('admin_comment', 1000)->nullable();
            $table->foreignId('user_id')->unique()->constrained();
            $table->foreignId('role_id')->constrained();
            $table->timestamps();
        });

        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->foreignId('profile_id')->constrained();
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
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('contact_types');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('qualifications');
        Schema::dropIfExists('account_log');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
