<?php

declare(strict_types=1);

namespace FlipPlay\Models;

class Movie extends Media
{
    private int $durationMinutes;

    private string $director;

    private string $quality;

    public function __construct(
        int $id,
        string $title,
        array $genres,
        float $rating,
        int $releaseYear,
        string $aura,
        string $synopsis,
        int $durationMinutes,
        string $director,
        string $quality = '4K Ultra HD'
    ) {
        parent::__construct($id, $title, $genres, $rating, $releaseYear, $aura, $synopsis);

        $this->durationMinutes = $durationMinutes;
        $this->director = $director;
        $this->quality = $quality;
    }

    public function getDurationMinutes(): int
    {
        return $this->durationMinutes;
    }

    public function getFormattedDuration(): string
    {
        $hours = intdiv($this->durationMinutes, 60);
        $minutes = $this->durationMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return sprintf('%dj %dm (%d menit)', $hours, $minutes, $this->durationMinutes);
        } elseif ($hours > 0) {
            return sprintf('%dj (%d menit)', $hours, $this->durationMinutes);
        }

        return sprintf('%d menit', $this->durationMinutes);
    }

    public function getDirector(): string
    {
        return $this->director;
    }

    public function getQuality(): string
    {
        return $this->quality;
    }

    public function getType(): string
    {
        return 'Movie';
    }

    public function getDetails(): string
    {
        return sprintf(
            "============================================================\n".
            "  🎬 DETAIL FILM: %s\n".
            "============================================================\n".
            "  ID Media       : #%d\n".
            "  Tipe           : %s\n".
            "  Sutradara      : %s\n".
            "  Tahun Rilis    : %d\n".
            "  Durasi         : %s\n".
            "  Kualitas Video : %s\n".
            "  Genre          : %s\n".
            "  Aura / Vibe    : %s\n".
            "  Rating         : %.1f / 10.0 ★\n".
            "  Sinopsis       : %s\n".
            '============================================================',
            $this->title,
            $this->id,
            $this->getType(),
            $this->director,
            $this->releaseYear,
            $this->getFormattedDuration(),
            $this->quality,
            $this->getGenreList(),
            $this->aura,
            $this->rating,
            $this->synopsis
        );
    }
}
