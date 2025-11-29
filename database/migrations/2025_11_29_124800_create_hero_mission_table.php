<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabla pivote con campos adicionales para gestión de grupos y seguimiento de estado.
     */
    public function up(): void
    {
        Schema::create('hero_mission', function (Blueprint $table) {
            $table->unsignedBigInteger('id_hero');
            $table->unsignedBigInteger('id_mission');
            
            // Campos adicionales para gestión de grupos y estado
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'failed'])->default('assigned');
            $table->string('group_name')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();

            // Definir claves foráneas
            $table->foreign('id_hero')
                ->references('id_hero')
                ->on('heroes')
                ->onDelete('cascade');

            $table->foreign('id_mission')
                ->references('id_mission')
                ->on('missions')
                ->onDelete('cascade');

            // Clave primaria compuesta
            $table->primary(['id_hero', 'id_mission']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_mission');
    }
};