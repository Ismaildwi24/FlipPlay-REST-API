<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isPatch = $this->isMethod('PATCH');
        $mediaRoute = $this->route('media');
        $mediaId = $mediaRoute instanceof Media ? $mediaRoute->id : $mediaRoute;

        return [
            'title' => [
                $isPatch ? 'sometimes' : 'required',
                'string',
                'max:255',
                $mediaId ? 'unique:media,title,' . $mediaId : 'unique:media,title',
            ],
            'type' => [
                $isPatch ? 'sometimes' : 'required',
                'string',
                'in:Anime,Movie',
            ],
            'genres' => [
                $isPatch ? 'sometimes' : 'required',
                'array',
                'min:1',
            ],
            'genres.*' => 'string|max:50',
            'rating' => [
                $isPatch ? 'sometimes' : 'required',
                'numeric',
                'min:0',
                'max:10',
            ],
            'release_year' => [
                $isPatch ? 'sometimes' : 'required',
                'integer',
                'min:1900',
                'max:2099',
            ],
            'aura' => [
                $isPatch ? 'sometimes' : 'required',
                'string',
                'max:100',
            ],
            'synopsis' => 'nullable|string',
            'episode_count' => 'nullable|integer|min:1',
            'studio' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'duration_minutes' => 'nullable|integer|min:1',
            'director' => 'nullable|string|max:255',
            'quality' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi',
            'title.unique' => 'Judul media sudah terdaftar',
            'type.required' => 'Tipe media wajib diisi',
            'type.in' => 'Tipe media harus berupa Anime atau Movie',
            'genres.required' => 'Genre wajib diisi',
            'genres.array' => 'Genre harus berupa array',
            'genres.min' => 'Minimal sertakan 1 genre',
            'rating.required' => 'Rating wajib diisi',
            'rating.numeric' => 'Rating harus berupa angka',
            'rating.min' => 'Rating minimal adalah 0',
            'rating.max' => 'Rating maksimal adalah 10',
            'release_year.required' => 'Tahun rilis wajib diisi',
            'release_year.integer' => 'Tahun rilis harus berupa angka tahun valid',
            'aura.required' => 'Aura / vibe media wajib diisi',
        ];
    }
}
