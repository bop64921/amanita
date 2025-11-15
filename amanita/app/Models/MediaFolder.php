<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaFolder extends Model
{
    protected $fillable = [
        'owner_id',
        'circle_id',
        'name',
        'description',
        'visibility',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function mediaItems(): HasMany
    {
        return $this->hasMany(MediaItem::class, 'folder_id');
    }

    // Con quién se comparte cuando visibility = 'custom'
    public function sharedWithUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_folder_user')
            ->withPivot(['can_view', 'can_edit'])
            ->withTimestamps();
    }
}