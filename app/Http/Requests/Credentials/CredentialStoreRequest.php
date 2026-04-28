<?php

namespace App\Http\Requests\Credentials;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CredentialStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id'   => ['required', 'string', Rule::unique('credentials', 'tenant_id')],
            'short_name'  => ['required', 'string', 'max:255'],
            'channel'     => ['required', 'string', 'max:255'],
            'host'        => ['required', 'string', 'max:255'],
            'key'         => ['required', 'string', 'max:255'],
            'city_name'   => ['required', 'string', 'max:255'],
            'city_shield' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg'],
            'state_name'  => ['nullable', 'string', 'max:255'],
            'address'     => ['nullable', 'string', 'max:255'],
            'zip_code'    => ['nullable', 'string', 'max:9'],
            'phone'       => ['nullable', 'string', 'max:20'],
            'cnpj'        => ['nullable', 'string', 'max:18'],
        ];
    }
}
