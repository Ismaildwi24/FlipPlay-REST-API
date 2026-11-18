<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaController extends Controller
{
    /**
     * Menampilkan daftar seluruh media di FlipPlay.
     * Mendukung eager loading relasi users serta query filter (pencarian judul, aura, dan tipe).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Media::query();

        // Filter pencarian berdasarkan judul atau sinopsis
        if ($request->filled('title')) {
            $query->search($request->query('title'));
        }

        // Filter berdasarkan Aura / Vibe
        if ($request->filled('aura')) {
            $query->byAura($request->query('aura'));
        }

        // Filter berdasarkan jenis media (Anime / Movie)
        if ($request->filled('type')) {
            $query->byType($request->query('type'));
        }

        // Eager loading relasi users dan pagination 10 data per halaman
        $media = $query->with('users')->paginate(10);

        return MediaResource::collection($media);
    }

    /**
     * Menampilkan detail satu media beserta relasi pengguna yang memasukkannya ke koleksi.
     * Menggunakan Route Model Binding (otomatis 404 jika ID tidak ditemukan).
     */
    public function show(Media $media): MediaResource
    {
        return new MediaResource($media->load('users'));
    }

    /**
     * Menambahkan media baru ke dalam sistem FlipPlay.
     * Divalidasi menggunakan StoreMediaRequest (Form Request Validation).
     * Mengembalikan status HTTP 201 Created.
     */
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Memperbarui data media (mendukung PUT dan PATCH).
     * Divalidasi menggunakan StoreMediaRequest.
     */
    public function update(StoreMediaRequest $request, Media $media): MediaResource
    {
        $media->update($request->validated());

        return new MediaResource($media);
    }

    /**
     * Menghapus media dari katalog FlipPlay.
     */
    public function destroy(Media $media): JsonResponse
    {
        $media->delete();

        return response()->json([
            'message' => 'Media berhasil dihapus'
        ], 200);
    }
}
