<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Check user doesn't already have a score for this week
        return !$this->route('week')
            ->scores()
            ->where('user_id', $this->user()->id)
            ->exists();
    }

    public function rules(): array
    {
        $maxScore = $this->route('week')->competition->max_score ?? 50;

        return [
            'score' => ['required', 'integer', 'min:0', 'max:' . $maxScore],
        ];
    }
}
