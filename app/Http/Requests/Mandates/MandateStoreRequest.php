<?php

namespace App\Http\Requests\Mandates;

use Illuminate\Foundation\Http\FormRequest;

class MandateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'      => 'required|string|max:255',
            'start_at'   => 'required|date',
            'end_at'     => 'required|date|after:start_at',
            'is_current' => 'boolean',
        ];
    }
}
