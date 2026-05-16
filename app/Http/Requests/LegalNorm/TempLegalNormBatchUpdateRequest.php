<?php

namespace App\Http\Requests\LegalNorm;

use Illuminate\Foundation\Http\FormRequest;

class TempLegalNormBatchUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids'             => ['nullable', 'array'],
            'ids.*'           => ['integer'],
            'norm_type_id'    => ['nullable', 'integer', 'exists:norm_types,id'],
            'norm_subject_id' => ['nullable', 'integer', 'exists:norm_subjects,id'],
        ];
    }
}
