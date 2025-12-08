<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CircleController extends Controller
{
    /**
     * Lista los círculos del usuario autenticado.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Círculos a los que pertenece el usuario, con info del pivot (role, joined_at, invited_by)
        $circles = $user->circles()
            ->withPivot(['role', 'joined_at', 'invited_by'])
            ->get();

        return response()->json($circles);
    }

    /**
     * Crea un nuevo círculo (familia) y añade al usuario como owner.
     */
   public function store(Request $request)
    {
        $user = $request->user();

        // 1) Validar datos (¡ahora incluimos la foto!)
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            // 'photo' debe coincidir con el nombre del campo en ApiClient.dart
            'photo'       => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $photoPath = null;

        // 2) Si se ha subido una foto, la guardamos
        if ($request->hasFile('photo')) {
            // La guardamos en 'storage/app/public/circles' y obtenemos la ruta
            $photoPath = $request->file('photo')->store('circles', 'public');
        }

        // 3) Crear el círculo (tabla circles) con la ruta de la foto
        $circle = Circle::create([
            'admin_id'    => $user->id,
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name'] . '-' . uniqid()),
            'description' => $validated['description'] ?? null,
            'photo_path'  => $photoPath, // Usamos la ruta de la foto guardada
            'is_default'  => false,
        ]);

        // 4) Añadir al usuario creador al pivot circle_user como `owner`
        $circle->users()->attach($user->id, [
            'role'       => 'owner',
            'joined_at'  => now(),
            'invited_by' => null,
        ]);

        // 5) Devolver el círculo recién creado (con la ruta de la foto)
        return response()->json($circle, 201);
    }
}