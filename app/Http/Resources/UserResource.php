<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'favorite_aura' => $this->favorite_aura,

            'media' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($item) {
                    $mediaData = [
                        'id' => $item->id,
                        'title' => $item->title,
                        'type' => $item->type,
                        'genres' => $item->genres,
                        'rating' => (float) $item->rating,
                        'release_year' => (int) $item->release_year,
                        'aura' => $item->aura,
                        'synopsis' => $item->synopsis,
                    ];

                    if ($item->pivot) {
                        $mediaData['collection_info'] = [
                            'status' => $item->pivot->status,
                            'user_rating' => $item->pivot->user_rating !== null ? (float) $item->pivot->user_rating : null,
                            'note' => $item->pivot->note,
                            'added_at' => $item->pivot->created_at?->toIso8601String(),
                        ];
                    }

                    return $mediaData;
                });
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
