<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'title' => 'sometimes|required|string|max:255',
            'excerpt' => 'sometimes|required|string|max:500',
            'content_markdown' => 'sometimes|required|string',
            'status' => ['sometimes', 'required', Rule::in(['draft', 'published', 'archived'])],
            'publish_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }
}
