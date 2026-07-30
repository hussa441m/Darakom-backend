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
            $table->string('project_code', 20)->unique()->nullable(); 
     
            $table->enum('craftsman_type', [
               'electricity',   // كهرباء
               'plumbing',      // سباكة
               'tiling',        // بلاط
                'ac',            // تكييف
               'gypsum',        // جبسنبورد
               'solar_energy',  // طاقة شمسية
               'painting'       // دهان
            ])->nullable();

              // نوع المناقصة (مستعجل أو عادي)
            $table->enum('tender_type', ['urgent', 'normal'])->default('normal');
            
            $table->string('title', 100);
            $table->enum('work_type', ['construction', 'finishing'])->comment('نوع العمل: إنشاء أو تشطيب');
            
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            // $table->unsignedSmallInteger('duration');
            
            $table->unsignedInteger('area'); //'in square meters (m2)'

            $table->string('location_details'); 
            $table->string('building_no', 15);
            
            $table->text('description'); 
            $table->enum('visibility', ['public', 'private'])->default('public');//طريقة ظهور المشروع
            $table->enum('invitation_type', [
              'public',
              'private'
            ])->default('public');
            $table->foreignId('provider_profile_id')->nullable()->constrained('profiles')->nullOnDelete();
            $table->unsignedSmallInteger('tender_duration')->default(3);

            $table->enum('tender_duration_unit', [
              'hour',
              'day'
            ])->default('day');
            $table->decimal('budget', 15, 2)->nullable(); 
            
            $table->enum('status', ['pending', 'new', 'active', 'completed'])->default('pending');
            $table->enum('execution_status', [
                'not_started',
                'in_progress',
                'paused',
                'finished'
            ])->default('not_started');

            
            $table->string('comment' , 1000)->nullable();
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->foreignId('province_id')->constrained();                                  
            $table->foreignId('project_type_id')->constrained();                                  
            $table->foreignId('client_id')->constrained('users');                                    
            $table->foreignId('performed_by')->nullable()->constrained('profiles')->nullOnDelete(); 
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