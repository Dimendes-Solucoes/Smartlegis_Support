<?php

namespace App\Http\Requests\Authors;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthorStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
                Rule::unique('authors')->where(function ($query) {
                    return $query->where('document_id', $this->document_id);
                }),
            ],
            'document_id' => [
                'required',
                'integer',
                Rule::exists('documents', 'id'),
            ],
        ];
    }
}
