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
            $table->text('text');      
            $table->enum('type', ['against_client','against_provider','against_me']);
            $table->enum('status', ['pending','under_review','resolved','rejected','closed'])->default('pending');
            $table->text('admin_response')->nullable();                       
                                         
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();                        
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('against_user_id')->constrained('users')->cascadeOnDelete();
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
