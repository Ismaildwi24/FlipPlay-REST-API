<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'genres' => $this->genres,
            'rating' => (float) $this->rating,
            'release_year' => (int) $this->release_year,
            'aura' => $this->aura,
            'synopsis' => $this->synopsis,

            // Atribut spesifik Anime (Modul 3)
            'anime_details' => $this->when($this->type === 'Anime', [
                'episode_count' => $this->episode_count,
                'studio' => $this->studio,
                'status' => $this->status,
            ]),

            // Atribut spesifik Movie (Modul 3)
            'movie_details' => $this->when($this->type === 'Movie', [
                'duration_minutes' => $this->duration_minutes,
                'formatted_duration' => $this->duration_minutes
                    ? intdiv($this->duration_minutes, 60) . 'j ' . ($this->duration_minutes % 60) . 'm (' . $this->duration_minutes . ' menit)'
                    : null,
                'director' => $this->director,
                'quality' => $this->quality,
            ]),

            // Relasi Users yang mengoleksi media ini (whenLoaded)
            'users' => $this->whenLoaded('users', function () {
                return $this->users->map(function ($user) {
                    $item = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'favorite_aura' => $user->favorite_aura,
                    ];

                    if ($user->pivot) {
                        $item['collection_info'] = [
                            'status' => $user->pivot->status,
                            'user_rating' => $user->pivot->user_rating !== null ? (float) $user->pivot->user_rating : null,
                            'note' => $user->pivot->note,
                            'added_at' => $user->pivot->created_at?->toIso8601String(),
                        ];
                    }

                    return $item;
                });
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
