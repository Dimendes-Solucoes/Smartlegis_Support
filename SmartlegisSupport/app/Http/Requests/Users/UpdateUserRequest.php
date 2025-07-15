<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->route('id'))],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nickname' => ['nullable', 'string', 'max:255'],
            'path_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg'],
            'existing_path_image' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:user_categories,id']
        ];
    }
}
