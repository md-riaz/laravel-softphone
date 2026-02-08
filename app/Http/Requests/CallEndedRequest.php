<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CallEndedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ended_at' => ['required', 'date'],
        ];
    }
}
