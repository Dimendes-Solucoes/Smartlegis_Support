<?php

namespace App\Http\Requests\Timers;

use Illuminate\Foundation\Http\FormRequest;

class TimerUpdateRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255',],
            'timer' => ['required', 'string', 'date_format:H:i:s',]
        ];
    }
}
