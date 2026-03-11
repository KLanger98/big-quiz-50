# Big Quiz 50

Quiz score tracking app for a friend group. Users compete weekly on a quiz scored out of 50.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.3
- **Frontend:** Vue 3 + Inertia 2 + TypeScript + Tailwind CSS 4 + shadcn-vue
- **Database:** MySQL (`big_quiz_50` on 127.0.0.1, user `root`, no password)
- **Auth:** Laravel Fortify
- **Animations:** canvas-confetti (winner celebration)
- **Routes:** Wayfinder auto-generates TypeScript route helpers

## Running the App

```bash
composer run dev    # Starts Laravel server, Vite, queue, and logs concurrently
```

App runs at http://localhost:8000

## Key Commands

```bash
php artisan migrate:fresh --seed    # Reset DB with test data
php artisan competition:generate-weeks  # Generate week records for active competitions
php artisan wayfinder:generate      # Regenerate TS route helpers (also runs on npm build)
npm run build                       # Production build
```

## Seeded Test Accounts

All passwords are `password`:
- **alice@example.com** (admin, "The Brainiacs")
- bob@example.com, charlie@example.com, diana@example.com, eve@example.com, frank@example.com

## Project Structure

```
app/
  Http/Controllers/          # Leaderboard, Week, Score, Comment, Reaction, Competition, Prize
  Http/Requests/             # StoreScore, StoreComment, UpdateCompetition
  Models/                    # User, Competition, Week, Score, Comment, Reaction
  Policies/                  # ScorePolicy, CommentPolicy (own resources only)
  Services/
    CompetitionService.php   # Week generation from competition date range
    LeaderboardService.php   # Weekly + overall standings calculations
  Console/Commands/
    GenerateWeeks.php        # Artisan command for manual week generation

resources/js/
  pages/
    Leaderboard/             # Index (overall + current week), Week (single week + confetti)
    Weeks/                   # Index (browse all), Show (scores, comments, reactions)
    Prizes/                  # Placeholder page
    Competition/             # Admin settings (name, dates, max score)
    settings/                # Profile (extended with team_name + profile picture)
  routes/                    # Wayfinder-generated route helpers (don't edit manually)
  actions/                   # Wayfinder-generated action helpers (don't edit manually)
```

## Important Patterns

- **CarbonImmutable is enabled** globally. Always reassign: `$date = $date->addWeek()`, never rely on mutation.
- **Inertia file uploads** require `form.post()` with `forceFormData: true` and `_method` spoofing for PATCH/PUT.
- **Shared Inertia data** (`HandleInertiaRequests.php`): `auth.user` includes `is_admin`, `team_name`, `profile_picture_url`.
- **Admin access**: Controlled by `is_admin` boolean on users table. Gate defined in `AppServiceProvider`.
- **Score constraints**: One score per user per week (DB unique constraint). Users can only edit/delete their own scores.

## Design

- Background: sage green `#677D74`
- Text: soft pink `#FBE3E0`
- Cards: `#728B82`
- Font: Helvetica, Verdana, Arial, sans-serif
- Single theme (no dark mode). CSS variables in `resources/css/app.css`.
