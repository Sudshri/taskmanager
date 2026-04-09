<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ordered_ids'   => ['required', 'array'],
            'ordered_ids.*' => ['integer', 'exists:tasks,id'],
        ];
    }
}
