<?php

declare(strict_types=1);

namespace FlipPlay\Models;

class Anime extends Media
{
    private int $episodeCount;

    private string $studio;

    private string $status;

    public function __construct(
        int $id,
        string $title,
        array $genres,
        float $rating,
        int $releaseYear,
        string $aura,
        string $synopsis,
        int $episodeCount,
        string $studio,
        string $status = 'Completed'
    ) {
        parent::__construct($id, $title, $genres, $rating, $releaseYear, $aura, $synopsis);

        $this->episodeCount = $episodeCount;
        $this->studio = $studio;
        $this->status = $status;
    }

    public function getEpisodeCount(): int
    {
        return $this->episodeCount;
    }

    public function getStudio(): string
    {
        return $this->studio;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getType(): string
    {
        return 'Anime';
    }

    public function getDetails(): string
    {
        return sprintf(
            "============================================================\n".
            "  📺 DETAIL ANIME: %s\n".
            "============================================================\n".
            "  ID Media       : #%d\n".
            "  Tipe           : %s\n".
            "  Studio         : %s\n".
            "  Tahun Rilis    : %d\n".
            "  Jumlah Episode : %d Episode (%s)\n".
            "  Genre          : %s\n".
            "  Aura / Vibe    : %s\n".
            "  Rating         : %.1f / 10.0 ★\n".
            "  Sinopsis       : %s\n".
            '============================================================',
            $this->title,
            $this->id,
            $this->getType(),
            $this->studio,
            $this->releaseYear,
            $this->episodeCount,
            $this->status,
            $this->getGenreList(),
            $this->aura,
            $this->rating,
            $this->synopsis
        );
    }
}
