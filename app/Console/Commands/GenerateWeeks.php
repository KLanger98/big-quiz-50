<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Services\CompetitionService;
use Illuminate\Console\Command;

class GenerateWeeks extends Command
{
    protected $signature = 'competition:generate-weeks {competition? : The competition ID}';

    protected $description = 'Generate week records for a competition based on its date range';

    public function handle(CompetitionService $service): int
    {
        $competitionId = $this->argument('competition');

        if ($competitionId) {
            $competition = Competition::findOrFail($competitionId);
            $service->generateWeeks($competition);
            $this->info("Generated weeks for competition: {$competition->name}");
        } else {
            $competitions = Competition::where('is_active', true)->get();
            foreach ($competitions as $competition) {
                $service->generateWeeks($competition);
                $this->info("Generated weeks for competition: {$competition->name}");
            }
        }

        return Command::SUCCESS;
    }
}
