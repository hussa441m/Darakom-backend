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
        Schema::create('project_types', function (Blueprint $table) {
            $table->id();
            $table->string('name' , 50)->unique();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->float('duration')->comment('in month');
            $table->float('area')->comment('in km');
            $table->string('location_details');
            $table->text('description');
            $table->string('building_no',15);
            
            $table->integer('budget')->nullable();
            
            $table->enum('status' , ['new' , 'active' , 'completed'])->default('new');
            
            $table->string('comment' , 1000)->nullable();
            $table->enum('rate' , ['1', '2' , '3' ,'4' ,'5'])->nullable();
            
            $table->foreignId('province_id')->constrained();                                    
            $table->foreignId('project_type_id')->constrained();                                    
            $table->foreignId('customer_id')->constrained('users');                                    
            $table->foreignId('performed_by')->nullable()->constrained('profiles');                        

            $table->timestamps();
        });

        Schema::create('project_type_role', function (Blueprint $table) {
            $table->foreignId('project_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->primary(['role_id', 'project_type_id']);     
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_type_role');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_types');
    }
};
