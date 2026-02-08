<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CallWrapupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string'],
            'disposition_ids' => ['nullable', 'array'],
            'disposition_ids.*' => ['exists:dispositions,id'],
        ];
    }
}
