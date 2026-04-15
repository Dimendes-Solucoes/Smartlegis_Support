<?php

namespace App\Http\Requests\Clicksign;

use Illuminate\Foundation\Http\FormRequest;

class ClicksignReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
            'start_date.date'         => 'A data inicial deve ser uma data válida.',
            'end_date.date'           => 'A data final deve ser uma data válida.',
        ];
    }

    public function filters(): array
    {
        return $this->only(['start_date', 'end_date']);
    }
}
