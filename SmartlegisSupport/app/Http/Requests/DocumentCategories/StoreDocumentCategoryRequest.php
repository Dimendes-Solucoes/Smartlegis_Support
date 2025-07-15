<?php

namespace App\Http\Requests\DocumentCategories;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentCategoryRequest extends FormRequest{

    public function authorize(): bool{
        return true;
    }

    public function rules(): array{
        return [
            'name'         => ['required', 'string', 'max:255', 'unique:document_categories,name'],
            'abbreviation' => ['nullable', 'string', 'max:50'],
        ];
    }
}