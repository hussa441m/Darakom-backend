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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('path' );
            $table->string('description' );
            $table->foreignId('documentable_id');                                                
            $table->string('documentable_type');                                                
            $table->foreignId('document_type_id')->constrained();                                                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_types');
    }
};
