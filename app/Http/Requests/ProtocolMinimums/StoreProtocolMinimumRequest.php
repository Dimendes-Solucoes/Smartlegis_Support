<?php

namespace App\Http\Requests\ProtocolMinimums;

use Illuminate\Foundation\Http\FormRequest;

class StoreProtocolMinimumRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'document_category_id' => ['required', 'integer', 'exists:document_categories,id'],
            'year'                 => ['required', 'integer', 'min:2000', 'max:2100'],
            'min_protocol'         => ['required', 'integer', 'min:1'],
        ];
    }
}
