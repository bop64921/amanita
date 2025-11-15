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
       Schema::create('circle_user', function (Blueprint $table) {
            $table->id();                                   // PK de la fila de membresía

            $table->unsignedBigInteger('circle_id');        // círculo al que pertenece
            $table->unsignedBigInteger('user_id');          // usuario dentro del círculo

            $table->enum('role', ['owner', 'member'])
                ->default('member');                        // rol dentro del círculo

            $table->timestamp('joined_at')->nullable();     // cuándo se unió al círculo
            $table->unsignedBigInteger('invited_by')->nullable(); // quién le invitó (otro user)

            $table->timestamps();                           // created_at / updated_at

            // Un mismo usuario no puede estar duplicado en el mismo círculo
            $table->unique(['circle_id', 'user_id']);

            // FKs
            $table->foreign('circle_id')
                ->references('id')->on('circles')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('invited_by')
                ->references('id')->on('users')
                ->nullOnDelete(); // si borran al que invitó, no queremos romper la fila
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circle_user');
    }
};
