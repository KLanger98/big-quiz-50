<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\Week;
use Carbon\CarbonImmutable;

class CompetitionService
{
    public function generateWeeks(Competition $competition): void
    {
        // Delete existing weeks that have no scores
        $competition->weeks()
            ->whereDoesntHave('scores')
            ->delete();

        $start = $competition->start_date->startOfWeek(CarbonImmutable::MONDAY);
        $end = $competition->end_date;
        $weekNumber = $competition->weeks()->max('week_number') ?? 0;

        $existingWeekStarts = $competition->weeks()
            ->pluck('start_date')
            ->map(fn ($d) => $d->format('Y-m-d'))
            ->toArray();

        while ($start->lte($end)) {
            $weekEnd = $start->endOfWeek(CarbonImmutable::SUNDAY);
            $startFormatted = $start->format('Y-m-d');

            if (!in_array($startFormatted, $existingWeekStarts)) {
                $weekNumber++;
                Week::create([
                    'competition_id' => $competition->id,
                    'week_number' => $weekNumber,
                    'start_date' => $start,
                    'end_date' => $weekEnd->gt($end) ? $end : $weekEnd,
                ]);
            }

            $start = $start->addWeek();
        }
    }
}
