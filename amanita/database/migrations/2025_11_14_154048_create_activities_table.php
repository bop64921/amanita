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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('circle_id')
                ->constrained('circles')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Tipo de actividad: task_created, task_completed, event_created, location_shared, media_added, etc.
            $table->string('type', 50);

            // Referencia genérica al recurso relacionado (tarea, evento, media...)
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('related_type', 50)->nullable();

            // Datos extra en JSON
            $table->json('data')->nullable();

            $table->timestamps();

            $table->index(['circle_id', 'created_at']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
