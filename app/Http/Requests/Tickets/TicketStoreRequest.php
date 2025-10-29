<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class TicketStoreRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'credential_ids' => ['nullable', 'array'],
            'credential_ids.*' => ['integer', 'exists:credentials,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:20480', 'mimes:pdf,doc,docx,jpg,jpeg,png,zip'], // 20MB max
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
            'title' => 'título',
            'description' => 'descrição',
            'credential_ids' => 'cidades',
            'attachments' => 'anexos',
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
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 5000 caracteres.',
            'credentials.*.exists' => 'Uma ou mais cidades selecionados não existem.',
            'attachments.*.file' => 'Um ou mais anexos são inválidos.',
            'attachments.*.max' => 'Cada anexo não pode exceder 10MB.',
            'attachments.*.mimes' => 'Formato de arquivo não permitido.',
        ];
    }
}
