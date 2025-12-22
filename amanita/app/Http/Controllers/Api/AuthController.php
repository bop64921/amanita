<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class AuthController extends Controller
{
    /**
     * Registro de usuario.
     * Crea el usuario y devuelve un token de Sanctum.
     */
    public function register(Request $request)
    {
        // 1. Comprobación manual: Si el email ya existe, devolvemos un error específico
        // para que el frontend pueda sugerir "Iniciar Sesión" o "Recuperar Contraseña".
        if ($request->has('email') && User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Este correo ya está registrado. ¿Quieres iniciar sesión o recuperar tu contraseña?',
                'code'    => 'EMAIL_EXISTS' // Código útil para que Flutter sepa qué popup mostrar
            ], 409); // 409 Conflict
        }

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
            // password_confirmation también debe venir en la request
        ]);

        $user = User::create([
            'first_name'        => $data['first_name'],
            'last_name'         => $data['last_name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'email_verified_at' => now(), // opcional, luego podemos hacer verificación real
        ]);

        // Creamos un token para la app (Flutter / Web)
        $token = $user->createToken('app_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login.
     * Devuelve un token si las credenciales son correctas.
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son correctas.'],
            ]);
        }

        // Opcional: borrar tokens anteriores si quieres un login "limpio"
        // $user->tokens()->delete();
         $user->last_login_at = now();
         $user->save();
        $token = $user->createToken('app_token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    /**
     * Devuelve el usuario autenticado según el token.
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Logout: revoca el token actual.
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }

    /**
     * Envía el link para resetear la contraseña.
     * El frontend recibirá un email con un token.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Usamos el broker de contraseñas de Laravel para enviar el link.
        // Esto busca el usuario, genera un token, lo guarda en la tabla `password_reset_tokens`
        // y envía una notificación (email) al usuario.
        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            // Si el email no existe, Laravel devuelve 'passwords.user'.
            // Lo traducimos a un error de validación para no revelar si el usuario existe.
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        }

        // Si todo va bien, Laravel devuelve 'passwords.sent'.
        return response()->json([
            'message' => trans($status)
        ]);
    }

    /**
     * Resetea la contraseña del usuario usando el token recibido.
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        // Usamos el broker para validar el token y resetear la contraseña.
        $status = Password::reset($validated, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            // Error: token inválido o el email no coincide.
            throw ValidationException::withMessages(['email' => [trans($status)]]);
        }

        return response()->json(['message' => trans($status)]);
    }
}