<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'favorite_aura',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_user')
            ->withPivot('status', 'user_rating', 'note')
            ->withTimestamps();
    }

    public function hasInCollection(int $mediaId): bool
    {
        return $this->media()->where('media_id', $mediaId)->exists();
    }
}
