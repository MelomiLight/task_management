<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|string|in:new,in progress,completed',
            'priority' => 'required|integer|min:1|max:3',
            'start_date' => 'nullable|date_format:Y-m-d H:i:s',
            'due_duration' => 'nullable|regex:/^\d+\s+days\s+\d+\s+hours\s+\d+\s+minutes$/',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}
