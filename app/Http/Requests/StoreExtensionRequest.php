<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExtensionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pbx_connection_id' => ['required', 'exists:pbx_connections,id'],
            'extension_number' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'user_id' => ['nullable', 'exists:users,id'],
            'display_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
