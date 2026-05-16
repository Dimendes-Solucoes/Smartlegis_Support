<?php

namespace App\Http\Requests\LegalNorm;

use Illuminate\Foundation\Http\FormRequest;

class TempLegalNormUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attachment' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
        ];
    }

    public function messages(): array
    {
        return [
            'attachment.required' => 'O arquivo é obrigatório.',
            'attachment.mimes'    => 'Apenas arquivos PDF, DOC e DOCX são aceitos.',
            'attachment.max'      => 'O arquivo não pode ultrapassar 20MB.',
        ];
    }
}
