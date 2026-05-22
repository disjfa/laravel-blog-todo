<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->todo()->customer_id;

        return [
            'blog_id' => ['nullable', 'uuid', Rule::exists('blogs', 'id')->where('customer_id', $customerId)],
            'platform_id' => ['sometimes', 'required', 'uuid', Rule::exists('platforms', 'id')],
            'title' => 'sometimes|required|string|max:255',
            'content_markdown' => 'nullable|string',
            'status' => ['sometimes', 'required', Rule::in(['todo', 'planned', 'in_progress', 'blocked', 'done'])],
            'position' => 'nullable|string|max:255',
            'due_at' => 'sometimes|required|date_format:Y-m-d H:i:s',
        ];
    }

    protected function todo()
    {
        return $this->route('todo');
    }
}
