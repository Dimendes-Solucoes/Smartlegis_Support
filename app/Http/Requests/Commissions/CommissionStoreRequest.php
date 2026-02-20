<?php

namespace App\Http\Requests\Commissions;

use App\Models\Tenancy\Comission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommissionStoreRequest extends FormRequest
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
            'comission_name' => ['required', 'string'],
            'type' => ['required', Rule::in(Comission::TYPES)]
        ];
    }
}
