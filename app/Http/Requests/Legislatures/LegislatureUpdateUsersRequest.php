<?php

namespace App\Http\Requests\Legislatures;

use Illuminate\Foundation\Http\FormRequest;

class LegislatureUpdateUsersRequest extends FormRequest
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
            'users'              => 'required|array',
            'users.*.user_id'    => 'required|exists:users,id',
            'users.*.start_date' => 'required|date',
            'users.*.end_date'   => 'nullable|date|after:users.*.start_date',
        ];
    }
}
