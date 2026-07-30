<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('previous_works', function (Blueprint $table) {

            $table->id();

            $table->string('title',150);

            $table->text('description');

            $table->string('location',150)->nullable();

            $table->date('date')->nullable();

            $table->foreignId('profile_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('previous_works');
    }
};