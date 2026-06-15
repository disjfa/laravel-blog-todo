<?php

namespace App\Http\Requests;

use App\Enums\TodoStatus;
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
            'external_url' => 'nullable|url|max:2048',
            'status' => ['required', Rule::enum(TodoStatus::class)],
            'position' => 'nullable|string|max:255',
            'due_at' => 'required|date_format:Y-m-d H:i:s',
        ];
    }

    protected function customer()
    {
        return $this->route('customer');
    }
}
