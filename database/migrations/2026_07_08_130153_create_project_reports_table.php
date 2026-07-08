<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_reports', function (Blueprint $table) {

            $table->id();

            $table->text('description');

            $table->string('image')->nullable();

            $table->enum('status',['pending','approved']);

            $table->foreignId('project_id')->constrained();

            $table->foreignId('user_id')->constrained();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_reports');
    }
};
