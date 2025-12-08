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
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('folder_id')
                ->constrained('media_folders')
                ->cascadeOnDelete();

            // Usuario que sube el archivo (puede coincidir o no con owner de la carpeta)
            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('circle_id')
                ->nullable()
                ->constrained('circles')
                ->nullOnDelete();

            $table->enum('type', ['photo', 'video', 'other'])
                ->default('photo');

            // Ruta en el storage (disco de Laravel, S3, etc.)
            $table->string('storage_path');

            // Miniatura (opcional) para vídeos/fotos
            $table->string('thumbnail_path')->nullable();

            $table->text('caption')->nullable(); // pie de foto / descripción

            $table->timestamp('taken_at')->nullable(); // cuándo se hizo la foto/vídeo (EXIF, etc.)

            $table->timestamps();

            $table->index(['folder_id']);
            $table->index(['circle_id']);
            $table->index(['owner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_items');
    }
};