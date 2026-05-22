<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->customer()->id;

        return [
            'blog_id' => ['nullable', 'uuid', Rule::exists('blogs', 'id')->where('customer_id', $customerId)],
            'platform_id' => ['required', 'uuid', Rule::exists('platforms', 'id')],
            'title' => 'required|string|max:255',
            'content_markdown' => 'nullable|string',
            'status' => ['required', Rule::in(['todo', 'planned', 'in_progress', 'blocked', 'done'])],
            'position' => 'nullable|string|max:255',
            'due_at' => 'required|date_format:Y-m-d H:i:s',
        ];
    }

    protected function customer()
    {
        return $this->route('customer');
    }
}
