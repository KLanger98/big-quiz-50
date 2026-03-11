<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCompetitionRequest;
use App\Models\Competition;
use App\Services\CompetitionService;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CompetitionController extends Controller
{
    public function edit(): Response
    {
        Gate::authorize('admin');

        $competition = Competition::where('is_active', true)->first();

        return Inertia::render('Competition/Edit', [
            'competition' => $competition,
        ]);
    }

    public function update(UpdateCompetitionRequest $request, CompetitionService $service): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('admin');

        $competition = Competition::where('is_active', true)->first();

        if ($competition) {
            $competition->update($request->validated());
        } else {
            $competition = Competition::create($request->validated());
        }

        $service->generateWeeks($competition);

        return back()->with('success', 'Competition settings updated.');
    }
}
