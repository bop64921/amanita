<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    // 1) Qué columnas se pueden rellenar en masa (create(), update())
    protected $fillable = [
        'circle_id',
        'creator_id',
        'assignee_id',
        'title',
        'description',
        'status',
        'due_at',
        'completed_at',
    ];

    // 2) Una tarea pertenece a un círculo
    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
        // Busca circle_id en tasks → id en circles
    }

    // 3) Usuario que creó la tarea
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
        // Le decimos la columna FK explícita: creator_id
    }

    // 4) Usuario al que se le asigna la tarea
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}