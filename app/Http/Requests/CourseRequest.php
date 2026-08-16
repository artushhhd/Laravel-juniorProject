<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isPost = $this->isMethod('post');

        return [
            'title'        => [$isPost ? 'required' : 'sometimes', 'string', 'max:255'],
            'slug'         => [$isPost ? 'required' : 'sometimes', 'string', 'max:255'], 
            'description'  => ['nullable', 'string'],
            'price'        => [$isPost ? 'required' : 'sometimes', 'numeric', 'min:0'],
            'status'       => [$isPost ? 'required' : 'sometimes', 'in:draft,published,archived'],
            'published_at' => ['nullable', 'date'],
            'image'        => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,svg',
                'max:2048'
            ],
        ];
    }
}
