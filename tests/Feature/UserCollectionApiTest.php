<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCollectionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_list_users(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'username',
                        'favorite_aura',
                        'media',
                    ],
                ],
            ]);
    }

    public function test_can_show_user_detail(): void
    {
        $user = User::first();

        $response = $this->getJson('/api/users/'.$user->id);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.username', $user->username);
    }

    public function test_can_get_user_collection(): void
    {
        $user = User::first();

        $response = $this->getJson('/api/users/'.$user->id.'/collection');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'collection_count',
                'collection',
            ]);
    }

    public function test_can_add_media_to_collection(): void
    {
        $user = User::where('username', 'ren_runner')->first();

        $media = Media::whereDoesntHave('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->first();

        $response = $this->postJson('/api/users/'.$user->id.'/collection', [
            'media_id' => $media->id,
            'status' => 'Watching',
            'user_rating' => 9.2,
            'note' => 'Seru sekali!',
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Media berhasil ditambahkan ke koleksi']);

        $this->assertTrue($user->fresh()->hasInCollection($media->id));
    }

    public function test_cannot_add_duplicate_media_to_collection(): void
    {
        $user = User::where('username', 'kenjiro')->first();
        $existingMedia = $user->media()->first();

        $response = $this->postJson('/api/users/'.$user->id.'/collection', [
            'media_id' => $existingMedia->id,
        ]);

        $response->assertStatus(409)
            ->assertJson(['message' => 'Media sudah ada di koleksi']);
    }

    public function test_validation_fails_when_adding_invalid_media_id(): void
    {
        $user = User::first();

        $response = $this->postJson('/api/users/'.$user->id.'/collection', [
            'media_id' => 999999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['media_id']);
    }

    public function test_can_remove_media_from_collection(): void
    {
        $user = User::where('username', 'ren_runner')->first();
        $media = $user->media()->first();

        $response = $this->deleteJson('/api/users/'.$user->id.'/collection/'.$media->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Media berhasil dihapus dari koleksi']);

        $this->assertFalse($user->fresh()->hasInCollection($media->id));
    }

    public function test_returns_404_when_removing_media_not_in_collection(): void
    {
        $user = User::where('username', 'ren_runner')->first();

        $media = Media::whereDoesntHave('users', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->first();

        $response = $this->deleteJson('/api/users/'.$user->id.'/collection/'.$media->id);

        $response->assertStatus(404)
            ->assertJson(['message' => 'Media tidak ditemukan di dalam koleksi pengguna']);
    }
}
