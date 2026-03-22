<?php

namespace App\Http\Requests\ProtocolMinimums;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProtocolMinimumRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'year'         => ['required', 'integer', 'min:2000', 'max:2100'],
            'min_protocol' => ['required', 'integer', 'min:1'],
        ];
    }
}
