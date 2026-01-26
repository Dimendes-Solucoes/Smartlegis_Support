<?php

namespace App\Http\Requests\DocumentModels;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'document_category_id' => ['required', 'integer', 'exists:document_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'usuário',
            'document_category_id' => 'categoria',
            'title' => 'título',
            'body' => 'conteúdo',
        ];
    }
}