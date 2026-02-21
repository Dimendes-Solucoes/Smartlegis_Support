<?php

namespace App\Http\Requests\CategoryParty;

use Illuminate\Foundation\Http\FormRequest;

class CategoryPartyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_party' => ['required', 'string', 'max:255'],
            'logo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name_party' => 'nome do partido',
            'logo'       => 'logo',
        ];
    }
}
