<?php

namespace App\Http\Requests\Transcriptions;

use Illuminate\Foundation\Http\FormRequest;

class TranscriptionFindExternalRequest extends FormRequest
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
            'code' => ['required', 'string'],
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
            'code' => [
                'description' => 'Código de validação ou autenticação',
                'example' => 'A1B2-C3D4-E5F6-G7H8',
            ],
        ];
    }
}
