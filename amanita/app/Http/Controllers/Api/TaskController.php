<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Listar tareas de un círculo.
     */
    public function index(Request $request, $circleId)
    {
        $user = $request->user();

        // Verificar que el usuario pertenece al círculo
        if (! $user->circles()->where('circles.id', $circleId)->exists()) {
            return response()->json(['message' => 'No tienes acceso a este círculo.'], 403);
        }

        $tasks = Task::where('circle_id', $circleId)
            ->with(['creator', 'assignee'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($tasks);
    }

    /**
     * Crear una tarea nueva.
     */
    public function store(Request $request, $circleId)
    {
        $user = $request->user();

        // Verificar acceso al círculo
        if (! $user->circles()->where('circles.id', $circleId)->exists()) {
            return response()->json(['message' => 'No tienes acceso a este círculo.'], 403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assignee_id' => 'nullable|exists:users,id',
            'due_at'      => 'nullable|date',
        ]);

        $task = Task::create([
            'circle_id'   => $circleId,
            'creator_id'  => $user->id,
            'assignee_id' => $validated['assignee_id'] ?? null,
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status'      => 'pending',
            'due_at'      => $validated['due_at'] ?? null,
        ]);

        return response()->json($task, 201);
    }

    /**
     * Actualizar estado de la tarea.
     */
    public function update(Request $request, Task $task)
    {
        $user = $request->user();

        // Verificar que el usuario pertenece al círculo de la tarea
        if (! $user->circles()->where('circles.id', $task->circle_id)->exists()) {
            return response()->json(['message' => 'No tienes acceso.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,done',
        ]);

        $task->update([
            'status' => $validated['status'],
            'completed_at' => $validated['status'] === 'done' ? now() : null,
        ]);

        return response()->json($task);
    }
}