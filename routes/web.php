<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PrizeController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\WeekController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/leaderboard');

    Route::get('leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
    Route::get('leaderboard/week/{week}', [LeaderboardController::class, 'week'])->name('leaderboard.week');

    Route::get('weeks', [WeekController::class, 'index'])->name('weeks.index');
    Route::get('weeks/{week}', [WeekController::class, 'show'])->name('weeks.show');

    Route::post('weeks/{week}/scores', [ScoreController::class, 'store'])->name('scores.store');
    Route::put('scores/{score}', [ScoreController::class, 'update'])->name('scores.update');
    Route::delete('scores/{score}', [ScoreController::class, 'destroy'])->name('scores.destroy');

    Route::post('weeks/{week}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::post('weeks/{week}/reactions', [ReactionController::class, 'toggle'])->name('reactions.toggle');

    Route::get('prizes', [PrizeController::class, 'index'])->name('prizes');

    Route::get('competition/settings', [CompetitionController::class, 'edit'])->name('competition.edit');
    Route::put('competition/settings', [CompetitionController::class, 'update'])->name('competition.update');
});

require __DIR__.'/settings.php';
