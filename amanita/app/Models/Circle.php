<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Circle extends Model
{
    // 👇 Aquí estaba el problema: solo tenías 'name'
    protected $fillable = [
        'admin_id',
        'name',
        'slug',
        'description',
        'photo_path',
        'is_default',
    ];

    // (Opcional pero útil) el usuario admin del círculo
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // 1) Miembros del círculo (usuarios), usando la tabla pivot circle_user
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'joined_at', 'invited_by'])
            ->withTimestamps();
        // Laravel asume pivot circle_user (circle_id, user_id)
    }

    // 2) Tareas del círculo
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
        // Busca circle_id en tasks
    }

    // 3) Eventos del círculo
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    // 4) Actividades (feed) del círculo
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    // 5) Media (fotos/vídeos) del círculo
    public function mediaItems(): HasMany
    {
        return $this->hasMany(MediaItem::class);
    }
}