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
        Schema::create('users', function (Blueprint $table) {
           $table->id();                                      // id autoincremental (PK)

$table->string('first_name');                      // nombre
$table->string('last_name');                       // apellidos

$table->string('email')->unique();                 // email único
$table->timestamp('email_verified_at')->nullable();// fecha verificación email (opcional)

$table->string('password');                        // hash de la contraseña

$table->string('photo_path')->nullable();          // ruta a la foto de perfil (puede no existir)
$table->timestamp('last_login_at')->nullable();    // último login (todavía no lo rellenamos, pero queda listo)

$table->rememberToken();                           // token para "recuérdame" en login web
$table->timestamps();  
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
