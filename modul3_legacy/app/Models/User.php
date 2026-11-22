<?php

declare(strict_types=1);

namespace FlipPlay\Models;

class User
{
    private int $id;

    private string $username;

    private string $name;

    private string $favoriteAura;

    private array $collection = [];

    public function __construct(
        int $id,
        string $username,
        string $name,
        string $favoriteAura = 'Binge-Mode'
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->name = $name;
        $this->favoriteAura = $favoriteAura;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFavoriteAura(): string
    {
        return $this->favoriteAura;
    }

    public function setFavoriteAura(string $favoriteAura): void
    {
        $this->favoriteAura = $favoriteAura;
    }

    public function addToCollection(
        Media $media,
        string $status = 'Watching',
        ?float $userRating = null,
        string $note = ''
    ): bool {
        if ($this->hasInCollection($media->getId())) {
            return false;
        }

        $this->collection[$media->getId()] = [
            'media' => $media,
            'status' => $status,
            'addedAt' => date('Y-m-d H:i'),
            'userRating' => $userRating,
            'note' => $note,
        ];

        return true;
    }

    public function removeFromCollection(int $mediaId): bool
    {
        if (isset($this->collection[$mediaId])) {
            unset($this->collection[$mediaId]);

            return true;
        }

        return false;
    }

    public function hasInCollection(int $mediaId): bool
    {
        return isset($this->collection[$mediaId]);
    }

    public function getCollection(): array
    {
        return array_values($this->collection);
    }

    public function getCollectionCount(): int
    {
        return count($this->collection);
    }

    public function getProfileSummary(): string
    {
        return sprintf(
            'User: %s (@%s) | Aura Favorit: %s | Koleksi Tersimpan: %d item',
            $this->name,
            $this->username,
            $this->favoriteAura,
            $this->getCollectionCount()
        );
    }
}
