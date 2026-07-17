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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->decimal('cost', 15, 2);
            $table->unsignedSmallInteger('duration');
            $table->enum('duration_unit', ['day','month','year'])->default('day');
             $table->text('provider_comment')->nullable();  //'نبذة عن العمل يكتبها المقاول عند تقديم العرض'
            $table->text('details');
           
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();                       
            $table->foreignId('offered_by')->constrained('profiles')->cascadeOnDelete();  
             
            $table->enum('status', ['pending', 'accepted', 'rejected', 'canceled'])->default('pending');
            $table->string('reject_reason', 500)->nullable();   //سبب الرفض                  

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
