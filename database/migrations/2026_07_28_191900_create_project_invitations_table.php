<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_invitations', function (Blueprint $table) {

            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_profile_id')->constrained('profiles')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted','declined'])->default('pending');
            $table->dateTime('expires_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            $table->unique([ 'project_id','provider_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_invitations');
    }
};