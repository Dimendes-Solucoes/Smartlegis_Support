<?php

namespace App\Http\Requests\Councilors;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class CouncilorUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\p{M}\s\'-]+ [\p{L}\p{M}\s\'-]+.*$/u'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->route('id'))],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'nickname' => ['nullable', 'string', 'max:255'],
            'path_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg'],
            'category_id' => ['required', 'exists:user_categories,id'],
            'party_id' => ['required', 'exists:category_parties,id'],
            'is_leader' => ['nullable', 'boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'name.regex' => 'O campo nome deve conter pelo menos duas palavras'
        ];
    }
}
