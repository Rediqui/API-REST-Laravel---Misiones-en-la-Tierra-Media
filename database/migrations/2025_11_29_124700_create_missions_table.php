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
        Schema::create('missions', function (Blueprint $table) {
            $table->id('id_mission');
            $table->string('title_mission');
            $table->text('description_mission')->nullable();
            $table->enum('difficulty_mission', ['easy', 'medium', 'hard', 'extreme', 'yes']);
            $table->enum('status_mission', ['starting','pending', 'in_progress', 'completed', 'cancelled', 'failed'])->default('pending');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};