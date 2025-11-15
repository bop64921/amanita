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
        Schema::create('user_locations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Puedes guardar ubicación por círculo/familia (opcional)
            $table->foreignId('circle_id')
                ->nullable()
                ->constrained('circles')
                ->nullOnDelete();

            // lat/lng con suficiente precisión
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->unsignedInteger('accuracy')->nullable(); // metros
            $table->string('provider', 50)->nullable(); // gps, network, etc.

            $table->timestamp('recorded_at'); // cuándo se tomó la lectura

            $table->timestamps();

            $table->index(['user_id', 'recorded_at']);
            $table->index(['circle_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_locations');
    }
};