<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'uuid' => ['required', 'uuid', 'unique:calls,uuid'],
            'extension_id' => ['required', 'exists:extensions,id'],
            'user_id' => ['required', 'exists:users,id'],
            'direction' => ['required', 'in:inbound,outbound'],
            'caller_number' => ['required', 'string', 'max:255'],
            'callee_number' => ['required', 'string', 'max:255'],
            'started_at' => ['required', 'date'],
        ];
    }
}
