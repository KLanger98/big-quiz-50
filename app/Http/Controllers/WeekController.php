<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Week;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WeekController extends Controller
{
    public function index(): Response
    {
        $competition = Competition::where('is_active', true)->first();

        $weeks = $competition
            ? $competition->weeks()
                ->withCount('scores')
                ->orderByDesc('week_number')
                ->get()
            : collect();

        return Inertia::render('Weeks/Index', [
            'competition' => $competition,
            'weeks' => $weeks,
        ]);
    }

    public function show(Request $request, Week $week): Response
    {
        $week->load([
            'competition',
            'scores.user:id,name,team_name,profile_picture',
            'comments.user:id,name,team_name,profile_picture',
            'reactions',
        ]);

        $userScore = $week->scores
            ->where('user_id', $request->user()->id)
            ->first();

        // Group reactions by emoji with counts and whether current user reacted
        $reactionSummary = $week->reactions
            ->groupBy('emoji')
            ->map(function ($reactions, $emoji) use ($request) {
                return [
                    'emoji' => $emoji,
                    'count' => $reactions->count(),
                    'reacted' => $reactions->contains('user_id', $request->user()->id),
                ];
            })
            ->values();

        return Inertia::render('Weeks/Show', [
            'week' => $week->only('id', 'week_number', 'start_date', 'end_date', 'is_locked', 'competition_id'),
            'competition' => $week->competition,
            'scores' => $week->scores->map(fn ($score) => [
                'id' => $score->id,
                'user_id' => $score->user_id,
                'score' => $score->score,
                'user' => [
                    'id' => $score->user->id,
                    'name' => $score->user->name,
                    'team_name' => $score->user->team_name,
                    'profile_picture' => $score->user->profile_picture_url,
                ],
            ])->sortByDesc('score')->values(),
            'userScore' => $userScore ? [
                'id' => $userScore->id,
                'score' => $userScore->score,
            ] : null,
            'comments' => $week->comments->map(fn ($comment) => [
                'id' => $comment->id,
                'body' => $comment->body,
                'created_at' => $comment->created_at->diffForHumans(),
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'team_name' => $comment->user->team_name,
                    'profile_picture' => $comment->user->profile_picture_url,
                ],
            ]),
            'reactions' => $reactionSummary,
        ]);
    }
}
