<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AuthCreateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
        ];
    }

    /**
     * Get the body parameters for Scribe documentation.
     *
     * @return array
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'O nome do usuário',
                'example' => 'João Silva',
            ],
            'email' => [
                'description' => 'O e-mail do usuário',
                'example' => 'joaosilva@exemplo.com',
            ],
            'password' => [
                'description' => 'A senha do usuário',
                'example' => 'Senha123!',
            ],
            'password_confirmation' => [
                'description' => 'A confirmação da senha do usuário deve ser igual à senha',
                'example' => 'Senha123!',
            ],
        ];
    }
}
