<?php

namespace App\Http\Requests\Resumes;

use App\Models\Resume;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResumeCreateRequest extends FormRequest
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
            'model' => ['required', 'string', Rule::in(Resume::MODELS)],
            'base' => ['required', 'string'],
            'input' => ['nullable', 'string']
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
            'model' => [
                'description' => 'Modelo de currículo a ser utilizado. Deve ser um dos valores pré-definidos.',
                'example' => Resume::MODEL_CUSTOM
            ],
            'base' => [
                'description' => 'Conteúdo base para geração do resumo',
                'example' => 'Transcrição da minha reunião com o cliente - falas...',
            ],
            'input' => [
                'description' => 'Entrada adicional personalizada',
                'example' => 'Instruções específicas para o resumo',
            ],
        ];
    }
}
