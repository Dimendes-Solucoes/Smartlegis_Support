<?php

namespace App\Http\Requests\LegalNorm;

use Illuminate\Foundation\Http\FormRequest;

class TempLegalNormUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'object'          => ['nullable', 'string', 'max:120'],
            'number'          => ['nullable', 'string', 'max:50'],
            'publication_date' => ['nullable', 'date_format:Y-m-d'],
            'norm_type_id'    => ['nullable', 'integer', 'exists:norm_types,id'],
            'norm_subject_id' => ['nullable', 'integer', 'exists:norm_subjects,id'],
            'is_active'       => ['nullable', 'boolean'],
        ];
    }
}
