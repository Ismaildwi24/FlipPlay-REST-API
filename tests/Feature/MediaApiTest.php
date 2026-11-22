<?php

namespace Tests\Feature;

use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_list_media_with_pagination(): void
    {
        $response = $this->getJson('/api/media');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'type',
                        'genres',
                        'rating',
                        'release_year',
                        'aura',
                        'synopsis',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_can_filter_media_by_title(): void
    {
        $response = $this->getJson('/api/media?title=Cyberpunk');

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Cyberpunk: Edgerunners']);
    }

    public function test_can_filter_media_by_aura(): void
    {
        $response = $this->getJson('/api/media?aura=Mind-Bending');

        $response->assertStatus(200);
        foreach ($response->json('data') as $item) {
            $this->assertEquals('Mind-Bending', $item['aura']);
        }
    }

    public function test_can_show_media_detail(): void
    {
        $media = Media::first();

        $response = $this->getJson('/api/media/'.$media->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $media->id)
            ->assertJsonPath('data.title', $media->title);
    }

    public function test_returns_404_when_media_not_found(): void
    {
        $response = $this->getJson('/api/media/999999');

        $response->assertStatus(404);
    }

    public function test_can_create_media(): void
    {
        $payload = [
            'title' => 'Chainsaw Man',
            'type' => 'Anime',
            'genres' => ['Action', 'Supernatural'],
            'rating' => 8.5,
            'release_year' => 2022,
            'aura' => 'High-Energy',
            'synopsis' => 'Denji bergabung dengan Public Safety Devil Hunters.',
            'episode_count' => 12,
            'studio' => 'MAPPA',
            'status' => 'Completed',
        ];

        $response = $this->postJson('/api/media', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Chainsaw Man');

        $this->assertDatabaseHas('media', ['title' => 'Chainsaw Man']);
    }

    public function test_validation_fails_when_creating_media_with_missing_fields(): void
    {
        $response = $this->postJson('/api/media', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'type', 'genres', 'rating', 'release_year', 'aura']);
    }

    public function test_cannot_create_media_with_duplicate_title(): void
    {
        $existing = Media::first();

        $response = $this->postJson('/api/media', [
            'title' => $existing->title,
            'type' => 'Anime',
            'genres' => ['Action'],
            'rating' => 8.0,
            'release_year' => 2024,
            'aura' => 'High-Energy',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_can_update_media_partially(): void
    {
        $media = Media::first();

        $response = $this->patchJson('/api/media/'.$media->id, [
            'rating' => 9.5,
        ]);

        $response->assertStatus(200);
        $this->assertEquals(9.5, (float) $response->json('data.rating'));
    }

    public function test_can_delete_media(): void
    {
        $media = Media::first();

        $response = $this->deleteJson('/api/media/'.$media->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Media berhasil dihapus']);

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }
}
