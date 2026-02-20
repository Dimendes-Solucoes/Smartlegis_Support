<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class TicketStoreAttachmentRequest extends FormRequest
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
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:20480', 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,txt'],
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
            'attachments' => 'anexos',
            'attachments.*' => 'arquivo anexo',
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
            'attachments.max' => 'Você pode anexar no máximo 10 arquivos por vez.',
            'attachments.*.file' => 'O item enviado deve ser um arquivo válido.',
            'attachments.*.max' => 'O arquivo não pode exceder 20 MB.',
            'attachments.*.mimes' => 'O tipo do arquivo anexo não é suportado. Tipos permitidos: JPEG, PNG, PDF, DOCX, XLSX.',
        ];
    }
}
