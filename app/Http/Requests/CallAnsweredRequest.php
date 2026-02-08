<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CallAnsweredRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answered_at' => ['required', 'date'],
        ];
    }
}
