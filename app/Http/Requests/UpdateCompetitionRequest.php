<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompetitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'max_score' => ['sometimes', 'integer', 'min:1', 'max:1000'],
        ];
    }
}
