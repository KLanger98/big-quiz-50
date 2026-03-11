<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Reaction;
use App\Models\Score;
use App\Models\User;
use App\Models\Week;
use Illuminate\Database\Seeder;

class ScoreSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $weeks = Week::where('end_date', '<', now())->orderBy('week_number')->take(10)->get();
        $emojis = ['🎉', '🔥', '😂', '👏', '💪', '🏆', '😭', '🤯'];

        foreach ($weeks as $week) {
            foreach ($users as $user) {
                // 80% chance a user has entered their score
                if (fake()->boolean(80)) {
                    Score::create([
                        'user_id' => $user->id,
                        'week_id' => $week->id,
                        'score' => fake()->numberBetween(15, 50),
                    ]);
                }
            }

            // Add some comments
            $commentCount = fake()->numberBetween(0, 4);
            for ($i = 0; $i < $commentCount; $i++) {
                Comment::create([
                    'user_id' => $users->random()->id,
                    'week_id' => $week->id,
                    'body' => fake()->randomElement([
                        'Great quiz this week!',
                        'That was a tough one!',
                        'Can\'t believe I missed that question about geography.',
                        'Finally cracked 40! 🎉',
                        'Who else got the music question wrong?',
                        'Rematch next week!',
                        'I blame the science questions.',
                        'My best score yet!',
                    ]),
                ]);
            }

            // Add some reactions
            foreach ($users->random(fake()->numberBetween(1, 4)) as $user) {
                Reaction::create([
                    'user_id' => $user->id,
                    'week_id' => $week->id,
                    'emoji' => fake()->randomElement($emojis),
                ]);
            }
        }
    }
}
