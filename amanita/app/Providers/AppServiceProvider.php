<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        ResetPassword::createUrlUsing(function ($user, string $token) {
            // Aquí va la URL de tu FRONTEND (la pantalla visual).
            // NO pongas la del backend (8085). Si usas Flutter, aquí iría tu Deep Link.
            // Usamos 'amanita://' para indicar que debe abrir la app.
            // De momento, copiarás el token manualmente del correo.
            $frontendUrl = 'amanita://reset-password';

            return $frontendUrl . '?token=' . $token . '&email=' . urlencode($user->getEmailForPasswordReset());
        });
    }
}
