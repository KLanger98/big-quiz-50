<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\Week;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeaderboardService
{
    public function weeklyStandings(Week $week): Collection
    {
        $scores = $week->scores()
            ->with('user:id,name,team_name,profile_picture')
            ->orderByDesc('score')
            ->get();

        $rank = 0;
        $lastScore = null;

        return $scores->map(function ($score) use (&$rank, &$lastScore) {
            if ($score->score !== $lastScore) {
                $rank++;
                $lastScore = $score->score;
            }

            return [
                'rank' => $rank,
                'user_id' => $score->user_id,
                'name' => $score->user->name,
                'team_name' => $score->user->team_name,
                'profile_picture' => $score->user->profile_picture_url,
                'score' => $score->score,
                'score_id' => $score->id,
                'is_winner' => $rank === 1,
            ];
        });
    }

    public function overallStandings(Competition $competition): Collection
    {
        $standings = DB::table('scores')
            ->join('weeks', 'scores.week_id', '=', 'weeks.id')
            ->join('users', 'scores.user_id', '=', 'users.id')
            ->where('weeks.competition_id', $competition->id)
            ->groupBy('scores.user_id', 'users.name', 'users.team_name', 'users.profile_picture')
            ->select([
                'scores.user_id',
                'users.name',
                'users.team_name',
                'users.profile_picture',
                DB::raw('SUM(scores.score) as total_score'),
                DB::raw('AVG(scores.score) as average_score'),
                DB::raw('COUNT(scores.id) as weeks_played'),
            ])
            ->orderByDesc('total_score')
            ->get();

        $rank = 0;
        $lastTotal = null;

        return $standings->map(function ($standing) use (&$rank, &$lastTotal) {
            if ($standing->total_score !== $lastTotal) {
                $rank++;
                $lastTotal = $standing->total_score;
            }

            return [
                'rank' => $rank,
                'user_id' => $standing->user_id,
                'name' => $standing->name,
                'team_name' => $standing->team_name,
                'profile_picture' => $standing->profile_picture
                    ? asset('storage/' . $standing->profile_picture)
                    : null,
                'total_score' => (int) $standing->total_score,
                'average_score' => round((float) $standing->average_score, 1),
                'weeks_played' => (int) $standing->weeks_played,
            ];
        });
    }
}
