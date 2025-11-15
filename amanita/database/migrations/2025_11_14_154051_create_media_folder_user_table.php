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
        Schema::create('media_folder_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('folder_id')
                ->constrained('media_folders')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Permisos por si en el futuro quieres que otros puedan subir/borrar
            $table->boolean('can_view')->default(true);
            $table->boolean('can_edit')->default(false);

            $table->timestamps();

            $table->unique(['folder_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_folder_user');
    }
};