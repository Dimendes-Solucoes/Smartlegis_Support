<?php

namespace App\Http\Requests\Legislatures;

use Illuminate\Foundation\Http\FormRequest;

class LegislatureStoreRequest extends FormRequest
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
            'title'      => 'required|string|max:255',
            'start_at'   => 'required|date',
            'end_at'     => 'required|date|after:start_at',
            'is_current' => 'boolean',
        ];
    }
}
