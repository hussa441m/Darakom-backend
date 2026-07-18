<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artisan_services', function (Blueprint $table) {

            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();;
            $table->foreignId('service_category_id')->constrained()->cascadeOnDelete();;
           $table->unique(['profile_id','service_category_id']);
            $table->timestamps();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artisan_services');
    }
};