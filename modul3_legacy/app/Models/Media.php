<?php

declare(strict_types=1);

namespace FlipPlay\Models;

abstract class Media
{
    protected int $id;
    protected string $title;
    protected array $genres;
    protected float $rating;
    protected int $releaseYear;
    protected string $aura;
    protected string $synopsis;

    public function __construct(
        int $id,
        string $title,
        array $genres,
        float $rating,
        int $releaseYear,
        string $aura,
        string $synopsis
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->genres = $genres;
        $this->rating = $rating;
        $this->releaseYear = $releaseYear;
        $this->aura = $aura;
        $this->synopsis = $synopsis;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function getGenreList(): string
    {
        return implode(', ', $this->genres);
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function getReleaseYear(): int
    {
        return $this->releaseYear;
    }

    public function getAura(): string
    {
        return $this->aura;
    }

    public function getSynopsis(): string
    {
        return $this->synopsis;
    }

    public function matchesKeyword(string $keyword): bool
    {
        $keyword = strtolower(trim($keyword));
        if (str_contains(strtolower($this->title), $keyword)) {
            return true;
        }

        foreach ($this->genres as $genre) {
            if (str_contains(strtolower($genre), $keyword)) {
                return true;
            }
        }

        return false;
    }

    public function getSummary(): string
    {
        return sprintf(
            '[%s] %s (%d) | Rating: %.1f★ | Aura: %s',
            $this->getType(),
            $this->title,
            $this->releaseYear,
            $this->rating,
            $this->aura
        );
    }

    abstract public function getType(): string;

    abstract public function getDetails(): string;
}
