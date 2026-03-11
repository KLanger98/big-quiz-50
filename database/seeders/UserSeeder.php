<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Alice', 'email' => 'alice@example.com', 'team_name' => 'The Brainiacs', 'is_admin' => true],
            ['name' => 'Bob', 'email' => 'bob@example.com', 'team_name' => 'Quiz Wizards'],
            ['name' => 'Charlie', 'email' => 'charlie@example.com', 'team_name' => 'Trivia Titans'],
            ['name' => 'Diana', 'email' => 'diana@example.com', 'team_name' => 'The Know-It-Alls'],
            ['name' => 'Eve', 'email' => 'eve@example.com', 'team_name' => 'Smarty Pants'],
            ['name' => 'Frank', 'email' => 'frank@example.com', 'team_name' => 'Fact Checkers'],
        ];

        foreach ($users as $userData) {
            User::factory()->create($userData);
        }
    }
}
