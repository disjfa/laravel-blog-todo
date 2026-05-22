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
        $blogId = $this->blog()->id;

        return [
            'title' => 'sometimes|required|string|max:255',
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('blogs', 'slug')->ignore($blogId, 'id')],
            'excerpt' => 'sometimes|required|string|max:500',
            'content_markdown' => 'sometimes|required|string',
            'status' => ['sometimes', 'required', Rule::in(['draft', 'published', 'archived'])],
            'publish_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    protected function blog()
    {
        return $this->route('blog');
    }
}
