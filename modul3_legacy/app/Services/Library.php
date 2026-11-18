<?php

declare(strict_types=1);

namespace FlipPlay\Services;

use FlipPlay\Models\Media;

class Library
{
    private string $name;
    private array $mediaItems = [];

    public function __construct(string $name = 'FlipPlay Central Library')
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addMedia(Media $media): void
    {
        $this->mediaItems[$media->getId()] = $media;
    }

    public function getAllMedia(): array
    {
        return array_values($this->mediaItems);
    }

    public function getTotalCount(): int
    {
        return count($this->mediaItems);
    }

    public function findById(int $id): ?Media
    {
        return $this->mediaItems[$id] ?? null;
    }

    public function searchByTitle(string $keyword): array
    {
        $results = [];
        foreach ($this->mediaItems as $media) {
            if ($media->matchesKeyword($keyword)) {
                $results[] = $media;
            }
        }
        return $results;
    }

    public function filterByAura(string $aura): array
    {
        $results = [];
        $auraUpper = strtoupper(trim($aura));

        foreach ($this->mediaItems as $media) {
            if (strtoupper($media->getAura()) === $auraUpper) {
                $results[] = $media;
            }
        }
        return $results;
    }

    public function filterByType(string $type): array
    {
        $results = [];
        $typeUpper = strtoupper(trim($type));

        foreach ($this->mediaItems as $media) {
            if (strtoupper($media->getType()) === $typeUpper) {
                $results[] = $media;
            }
        }
        return $results;
    }

    public function getStatistics(): array
    {
        $total = count($this->mediaItems);
        if ($total === 0) {
            return [
                'total' => 0,
                'animeCount' => 0,
                'movieCount' => 0,
                'averageRating' => 0.0
            ];
        }

        $animeCount = 0;
        $movieCount = 0;
        $totalScore = 0.0;

        foreach ($this->mediaItems as $media) {
            if ($media->getType() === 'Anime') {
                $animeCount++;
            } elseif ($media->getType() === 'Movie') {
                $movieCount++;
            }
            $totalScore += $media->getRating();
        }

        return [
            'total' => $total,
            'animeCount' => $animeCount,
            'movieCount' => $movieCount,
            'averageRating' => round($totalScore / $total, 2)
        ];
    }

    public function sortByRating(bool $descending = true): array
    {
        $items = array_values($this->mediaItems);
        usort($items, function (Media $a, Media $b) use ($descending): int {
            return $descending 
                ? ($b->getRating() <=> $a->getRating()) 
                : ($a->getRating() <=> $b->getRating());
        });
        return $items;
    }

    public function sortByYear(bool $descending = true): array
    {
        $items = array_values($this->mediaItems);
        usort($items, function (Media $a, Media $b) use ($descending): int {
            return $descending 
                ? ($b->getReleaseYear() <=> $a->getReleaseYear()) 
                : ($a->getReleaseYear() <=> $b->getReleaseYear());
        });
        return $items;
    }
}
