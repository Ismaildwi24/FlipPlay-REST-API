<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCollectionRequest;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::with('media')->paginate(10);

        return UserResource::collection($users);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user->load('media'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'favorite_aura' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Nama pengguna wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
        ]);

        $user = User::create($validated);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, User $user): UserResource
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,'.$user->id,
            'email' => 'sometimes|nullable|email|unique:users,email,'.$user->id,
            'favorite_aura' => 'sometimes|string|max:100',
        ], [
            'username.unique' => 'Username sudah digunakan',
            'email.unique' => 'Email sudah digunakan',
        ]);

        $user->update($validated);

        return new UserResource($user);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus',
        ], 200);
    }

    public function addToCollection(AddToCollectionRequest $request, User $user): JsonResponse
    {
        $mediaId = (int) $request->validated('media_id');

        if ($user->hasInCollection($mediaId)) {
            return response()->json([
                'message' => 'Media sudah ada di koleksi',
            ], 409);
        }

        $user->media()->attach($mediaId, [
            'status' => $request->validated('status', 'Watching'),
            'user_rating' => $request->validated('user_rating'),
            'note' => $request->validated('note', ''),
        ]);

        return response()->json([
            'message' => 'Media berhasil ditambahkan ke koleksi',
            'data' => new UserResource($user->load('media')),
        ], 201);
    }

    public function removeFromCollection(User $user, Media $media): JsonResponse
    {

        if (! $user->hasInCollection($media->id)) {
            return response()->json([
                'message' => 'Media tidak ditemukan di dalam koleksi pengguna',
            ], 404);
        }

        $user->media()->detach($media->id);

        return response()->json([
            'message' => 'Media berhasil dihapus dari koleksi',
        ], 200);
    }

    public function getCollection(User $user): JsonResponse
    {
        $user->load('media');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'favorite_aura' => $user->favorite_aura,
            ],
            'collection_count' => $user->media->count(),
            'collection' => (new UserResource($user))->resolve()['media'],
        ], 200);
    }
}
