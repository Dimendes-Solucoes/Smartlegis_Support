<?php

namespace App\Http\Requests\Documents;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'protocol_number' => 'nullable|string|max:255',
            'document_status_vote_id' => 'required|integer|exists:document_status_vote,id',
            'document_status_movement_id' => 'required|integer|exists:document_status_movement,id',
        ];
    }
}