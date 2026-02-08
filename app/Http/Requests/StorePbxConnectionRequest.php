<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePbxConnectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'exists:companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'wss_url' => ['required', 'string', 'max:255'],
            'stun_server' => ['nullable', 'string', 'max:255'],
            'turn_server' => ['nullable', 'string', 'max:255'],
            'turn_username' => ['nullable', 'string', 'max:255'],
            'turn_password' => ['nullable', 'string', 'max:255'],
        ];
    }
}
