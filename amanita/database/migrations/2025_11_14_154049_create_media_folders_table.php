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
        Schema::create('media_folders', function (Blueprint $table) {
            $table->id();

            // Dueño de la carpeta
            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Opcional: asociada a una familia/círculo concreto
            $table->foreignId('circle_id')
                ->nullable()
                ->constrained('circles')
                ->nullOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            // Quién puede ver la carpeta
            // private: solo el dueño
            // circle: todos los del círculo
            // custom: se controla por tabla pivot media_folder_user
            $table->enum('visibility', ['private', 'circle', 'custom'])
                ->default('private');

            $table->timestamps();

            $table->index(['owner_id', 'circle_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_folders');
    }
};