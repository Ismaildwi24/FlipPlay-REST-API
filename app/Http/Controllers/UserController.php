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
    /**
     * Menampilkan seluruh pengguna beserta koleksi media masing-masing (eager loading).
     */
    public function index(): AnonymousResourceCollection
    {
        $users = User::with('media')->paginate(10);
        return UserResource::collection($users);
    }

    /**
     * Menampilkan profil satu pengguna beserta seluruh koleksi medianya.
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user->load('media'));
    }

    /**
     * Membuat pengguna baru.
     */
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

    /**
     * Memperbarui profil pengguna.
     */
    public function update(Request $request, User $user): UserResource
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|nullable|email|unique:users,email,' . $user->id,
            'favorite_aura' => 'sometimes|string|max:100',
        ], [
            'username.unique' => 'Username sudah digunakan',
            'email.unique' => 'Email sudah digunakan',
        ]);

        $user->update($validated);

        return new UserResource($user);
    }

    /**
     * Menghapus pengguna dari sistem.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus'
        ], 200);
    }

    /**
     * CUSTOM ACTION: Menambahkan media ke dalam koleksi pribadi pengguna.
     * Menggunakan Form Request Validation dan Error Handling 409 jika duplikat (Gambar 4.16).
     */
    public function addToCollection(AddToCollectionRequest $request, User $user): JsonResponse
    {
        $mediaId = (int) $request->validated('media_id');

        // Cek apakah media sudah ada di dalam koleksi pengguna (Conflict - 409)
        if ($user->hasInCollection($mediaId)) {
            return response()->json([
                'message' => 'Media sudah ada di koleksi'
            ], 409);
        }

        // Tambahkan relasi ke tabel pivot 'media_user' dengan metadata opsional
        $user->media()->attach($mediaId, [
            'status' => $request->validated('status', 'Watching'),
            'user_rating' => $request->validated('user_rating'),
            'note' => $request->validated('note', ''),
        ]);

        return response()->json([
            'message' => 'Media berhasil ditambahkan ke koleksi',
            'data' => new UserResource($user->load('media'))
        ], 201);
    }

    /**
     * CUSTOM ACTION: Menghapus media dari koleksi pribadi pengguna.
     */
    public function removeFromCollection(User $user, Media $media): JsonResponse
    {
        // Cek apakah media memang ada di dalam koleksi pengguna
        if (!$user->hasInCollection($media->id)) {
            return response()->json([
                'message' => 'Media tidak ditemukan di dalam koleksi pengguna'
            ], 404);
        }

        // Lepaskan relasi di tabel pivot
        $user->media()->detach($media->id);

        return response()->json([
            'message' => 'Media berhasil dihapus dari koleksi'
        ], 200);
    }

    /**
     * CUSTOM ACTION: Menampilkan hanya daftar koleksi media milik pengguna tertentu.
     */
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
