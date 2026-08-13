<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('previous_work_images', function (Blueprint $table) {
            $table->id();
            
            $table->string('path');
            $table->boolean('is_cover')->default(false); // <--- إضافة حقل الغلاف هنا

            $table->foreignId('previous_work_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('previous_work_images');
    }
};