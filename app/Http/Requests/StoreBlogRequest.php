<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content_markdown' => 'required|string',
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'publish_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }
}
