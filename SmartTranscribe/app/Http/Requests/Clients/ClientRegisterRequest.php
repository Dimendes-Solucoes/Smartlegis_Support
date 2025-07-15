<?php

namespace App\Http\Requests\Clients;

use Illuminate\Foundation\Http\FormRequest;

class ClientRegisterRequest extends FormRequest
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
            'website_name' => ['required', 'string', 'max:255'],
            'webhook_url' => ['required', 'url']
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
            'website_name' => [
                'description' => 'O nome do website ou aplicação',
                'example' => 'Meu Site Incrível',
            ],
            'webhook_url' => [
                'description' => 'URL de webhook para receber notificações (deve ser uma URL válida)',
                'example' => 'https://meusite.com/webhook/receiver',
            ],
        ];
    }
}
