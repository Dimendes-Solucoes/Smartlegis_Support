<?php

namespace App\Http\Requests\Commissions;

use App\Models\Tenancy\ComissionUser;
use Illuminate\Foundation\Http\FormRequest;

class CommissionUpdateUsersRequest extends FormRequest
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
            'users' => ['nullable', 'array'],
            'users.*.id' => ['required', 'integer', 'exists:users,id'],
            'users.*.function' => ['required', 'integer', 'in:' . implode(',', ComissionUser::FUNCTIONS)],
        ];
    }
}
