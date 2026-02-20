<?php

namespace App\Http\Requests\Tickets;

use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'status' => ['required', 'string', Rule::enum(TicketStatus::class)],
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
            'status' => 'status do ticket',
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
            'status.required' => 'O status do ticket é obrigatório.',
            'status.enum' => 'O status do ticket selecionado é inválido.',
            'credential_ids.*.exists' => 'Uma ou mais cidades selecionados não existem.'
        ];
    }
}
