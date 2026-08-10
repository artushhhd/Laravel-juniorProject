<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
        ];
    }
    public function messages(): array
    {
        return [
            'content.required' => 'Comment text cannot be empty.',
            'content.max' => 'The comment is too long (maximum 1000 characters).',
        ];
    }
}
