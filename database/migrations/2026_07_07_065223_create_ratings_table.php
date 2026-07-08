<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('rate');
            $table->string('comment',1000)->nullable();

            $table->foreignId('project_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('to_user_id')->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};