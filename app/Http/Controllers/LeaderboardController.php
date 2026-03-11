<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Week;
use App\Services\LeaderboardService;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class LeaderboardController extends Controller
{
    public function __construct(private LeaderboardService $leaderboard) {}

    public function index(): Response
    {
        $competition = Competition::where('is_active', true)->first();

        if (!$competition) {
            return Inertia::render('Leaderboard/Index', [
                'competition' => null,
                'overallStandings' => [],
                'currentWeek' => null,
                'currentWeekStandings' => [],
            ]);
        }

        $currentWeek = $competition->weeks()
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->first();

        // If no current week, get the latest week with scores
        if (!$currentWeek) {
            $currentWeek = $competition->weeks()
                ->whereHas('scores')
                ->orderByDesc('week_number')
                ->first();
        }

        return Inertia::render('Leaderboard/Index', [
            'competition' => $competition,
            'overallStandings' => $this->leaderboard->overallStandings($competition),
            'currentWeek' => $currentWeek,
            'currentWeekStandings' => $currentWeek
                ? $this->leaderboard->weeklyStandings($currentWeek)
                : [],
        ]);
    }

    public function week(Week $week): Response
    {
        $week->load('competition');

        $prevWeek = Week::where('competition_id', $week->competition_id)
            ->where('week_number', '<', $week->week_number)
            ->orderByDesc('week_number')
            ->first();

        $nextWeek = Week::where('competition_id', $week->competition_id)
            ->where('week_number', '>', $week->week_number)
            ->orderBy('week_number')
            ->first();

        return Inertia::render('Leaderboard/Week', [
            'week' => array_merge($week->toArray(), [
                'prev_week_id' => $prevWeek?->id,
                'next_week_id' => $nextWeek?->id,
            ]),
            'standings' => $this->leaderboard->weeklyStandings($week),
            'competition' => $week->competition,
        ]);
    }
}
