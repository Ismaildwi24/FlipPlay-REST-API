<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'favorite_aura',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi Many-to-Many: Satu Pengguna dapat mengoleksi banyak Media.
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_user')
            ->withPivot('status', 'user_rating', 'note')
            ->withTimestamps();
    }

    /**
     * Cek apakah media tertentu sudah ada di koleksi pengguna.
     */
    public function hasInCollection(int $mediaId): bool
    {
        return $this->media()->where('media_id', $mediaId)->exists();
    }
}
