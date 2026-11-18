<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     */
    protected $table = 'media';

    /**
     * Kolom yang boleh diisi melalui mass-assignment.
     */
    protected $fillable = [
        'title',
        'type',
        'genres',
        'rating',
        'release_year',
        'aura',
        'synopsis',
        'episode_count',
        'studio',
        'status',
        'duration_minutes',
        'director',
        'quality',
    ];

    /**
     * Casting tipe data kolom otomatis.
     */
    protected $casts = [
        'genres' => 'array',
        'rating' => 'float',
        'release_year' => 'integer',
        'episode_count' => 'integer',
        'duration_minutes' => 'integer',
    ];

    /**
     * Relasi Many-to-Many: Satu Media dapat disimpan oleh banyak Pengguna (User).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_user')
            ->withPivot('status', 'user_rating', 'note')
            ->withTimestamps();
    }

    /**
     * Query Scope: Pencarian berdasarkan judul media atau genre.
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (blank($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('synopsis', 'like', "%{$keyword}%");
        });
    }

    /**
     * Query Scope: Filter berdasarkan Aura / Vibe.
     */
    public function scopeByAura(Builder $query, ?string $aura): Builder
    {
        if (blank($aura)) {
            return $query;
        }

        return $query->where('aura', $aura);
    }

    /**
     * Query Scope: Filter berdasarkan jenis media ('Anime' atau 'Movie').
     */
    public function scopeByType(Builder $query, ?string $type): Builder
    {
        if (blank($type)) {
            return $query;
        }

        return $query->where('type', $type);
    }
}
