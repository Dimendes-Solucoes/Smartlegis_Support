<?php

namespace App\Http\Requests\LegalNorm;

use Illuminate\Foundation\Http\FormRequest;

class TempLegalNormBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids'   => ['nullable', 'array'],
            'ids.*' => ['integer'],
        ];
    }
}
