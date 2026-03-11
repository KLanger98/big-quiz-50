<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Services\CompetitionService;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        $competition = Competition::create([
            'name' => 'The Big Quiz 2026',
            'start_date' => '2026-01-05',
            'end_date' => '2026-12-28',
            'max_score' => 50,
            'is_active' => true,
        ]);

        (new CompetitionService())->generateWeeks($competition);
    }
}
