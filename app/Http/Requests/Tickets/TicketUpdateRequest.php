<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class TicketUpdateRequest extends FormRequest
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
            'ticket_type_id' => ['required', 'integer', 'exists:ticket_types,id'],
            'ticket_status_id' => ['required', 'integer', 'exists:ticket_status,id'],
            'credential_ids' => ['nullable', 'array'],
            'credential_ids.*' => ['integer', 'exists:credentials,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'ticket_type_id' => 'tipo de ticket',
            'ticket_status_id' => 'status do ticket',
            'credential_ids' => 'cidades',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ticket_type_id.required' => 'O tipo de ticket é obrigatório.',
            'ticket_type_id.exists' => 'O tipo de ticket selecionado não existe.',
            'ticket_status_id.required' => 'O status do ticket é obrigatório.',
            'ticket_status_id.exists' => 'O status selecionado não existe.',
            'credential_ids.*.exists' => 'Uma ou mais cidades selecionados não existem.'
        ];
    }
}
