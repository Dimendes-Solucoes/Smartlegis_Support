<?php

namespace App\Http\Requests\Sessions;

use Illuminate\Foundation\Http\FormRequest;

class SessionUpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'expediente_documents'   => ['present', 'array'],
            'expediente_documents.*' => ['integer', 'exists:documents,id'],
            'ordem_do_dia_documents'   => ['present', 'array'],
            'ordem_do_dia_documents.*' => ['integer', 'exists:documents,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'expediente_documents.*.exists' => 'O documento (ID :input) não foi encontrado no expediente.',
            'ordem_do_dia_documents.*.exists' => 'O documento (ID :input) não foi encontrado na ordem do dia.',
        ];
    }
}