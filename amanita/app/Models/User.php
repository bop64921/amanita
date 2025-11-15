<?php 

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    // ...

    // 1) Círculos a los que pertenece el usuario
    public function circles(): BelongsToMany
    {
        return $this->belongsToMany(Circle::class)
            ->withPivot(['role', 'joined_at', 'invited_by'])
            ->withTimestamps();
    }

    // 2) Tareas que este usuario ha creado
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'creator_id');
    }

    // 3) Tareas que le han asignado a este usuario
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

    // 4) Actividad generada por este usuario
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    // 5) Carpetas de media creadas por este usuario
    public function mediaFolders(): HasMany
    {
        return $this->hasMany(MediaFolder::class, 'owner_id');
    }

    // 6) Media subida por este usuario
    public function mediaItems(): HasMany
    {
        return $this->hasMany(MediaItem::class, 'owner_id');
    }

    // 7) Ubicaciones del usuario (historial)
    public function locations(): HasMany
    {
        return $this->hasMany(UserLocation::class);
    }
}
