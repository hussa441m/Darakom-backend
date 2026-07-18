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
            $table->unsignedTinyInteger('reported_progress')->nullable();//نسبة الإنجاز
            $table->enum('status',['pending','approved','rejected'])->default('pending');

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('step_id')->nullable()->constrained('steps')->nullOnDelete();;
           

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_reports');
    }
};
