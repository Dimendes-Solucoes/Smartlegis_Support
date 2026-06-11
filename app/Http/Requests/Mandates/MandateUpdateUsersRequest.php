<?php

namespace App\Http\Requests\Mandates;

use Illuminate\Foundation\Http\FormRequest;

class MandateUpdateUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'users'                      => 'required|array',
            'users.*.user_id'            => 'required|exists:users,id',
            'users.*.category_party_id'  => 'nullable|exists:category_parties,id',
            'users.*.start_date'         => 'required|date',
            'users.*.end_date'           => 'nullable|date|after_or_equal:users.*.start_date',
        ];
    }
}
