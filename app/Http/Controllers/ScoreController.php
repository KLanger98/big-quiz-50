<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScoreRequest;
use App\Models\Score;
use App\Models\Week;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function store(StoreScoreRequest $request, Week $week): RedirectResponse
    {
        $week->scores()->create([
            'user_id' => $request->user()->id,
            'score' => $request->validated('score'),
        ]);

        return back();
    }

    public function update(Request $request, Score $score): RedirectResponse
    {
        $this->authorize('update', $score);

        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:' . ($score->week->competition->max_score ?? 50)],
        ]);

        $score->update($validated);

        return back();
    }

    public function destroy(Score $score): RedirectResponse
    {
        $this->authorize('delete', $score);

        $score->delete();

        return back();
    }
}
