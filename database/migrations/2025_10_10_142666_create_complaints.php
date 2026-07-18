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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();            
            $table->string('text');      
            $table->enum('status', ['pending', 'resolved', 'rejected', 'closed'])->default('pending');
            $table->text('admin_response')->nullable();    //'رد الأدمن أو الإجراء المتخذ لحل الشكوى'                    
                                         
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();                        
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();             

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
