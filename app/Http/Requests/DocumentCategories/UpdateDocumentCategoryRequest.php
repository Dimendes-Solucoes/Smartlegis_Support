<?php

namespace App\Http\Requests\DocumentCategories;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('document_categories')->ignore($categoryId)
            ],
            'abbreviation' => ['nullable', 'string', 'max:50'],
            'min_protocol' => ['required', 'integer', 'min:1']
        ];
    }
}
