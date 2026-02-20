<?php

namespace App\Http\Requests\Credentials;

use Illuminate\Foundation\Http\FormRequest;

class CredentialUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'short_name' => ['required', 'string', 'max:255'],
            'channel' => ['required', 'string', 'max:255'],
            'host' => ['required', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:255'],
            'city_name' => ['required', 'string', 'max:255'],
            'city_shield' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg'],
        ];
    }
}