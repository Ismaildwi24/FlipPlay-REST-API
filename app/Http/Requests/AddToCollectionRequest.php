<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'media_id' => 'required|integer|exists:media,id',
            'status' => 'nullable|string|in:Watching,Completed,Plan to Watch,Dropped',
            'user_rating' => 'nullable|numeric|min:0|max:10',
            'note' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'media_id.required' => 'ID media wajib disertakan',
            'media_id.integer' => 'ID media harus berupa bilangan bulat',
            'media_id.exists' => 'Media dengan ID tersebut tidak ditemukan',
            'status.in' => 'Status tonton harus salah satu dari: Watching, Completed, Plan to Watch, Dropped',
            'user_rating.numeric' => 'Rating pengguna harus berupa angka',
            'user_rating.min' => 'Rating pengguna minimal 0',
            'user_rating.max' => 'Rating pengguna maksimal 10',
            'note.max' => 'Catatan maksimal 500 karakter',
        ];
    }
}
