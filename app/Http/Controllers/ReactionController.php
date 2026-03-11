<?php

namespace App\Http\Controllers;

use App\Models\Week;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReactionController extends Controller
{
    public function toggle(Request $request, Week $week): RedirectResponse
    {
        $validated = $request->validate([
            'emoji' => ['required', 'string', 'max:8'],
        ]);

        $existing = $week->reactions()
            ->where('user_id', $request->user()->id)
            ->where('emoji', $validated['emoji'])
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            $week->reactions()->create([
                'user_id' => $request->user()->id,
                'emoji' => $validated['emoji'],
            ]);
        }

        return back();
    }
}
