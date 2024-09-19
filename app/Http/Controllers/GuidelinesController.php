<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGuidelineRequest;
use App\Http\Requests\UpdateGuidelineRequest;
use App\Models\Guideline;
use App\Models\GuidelineBullet;
use App\Models\GuidelineTicket;
use App\Models\Team;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class GuidelinesController extends Controller
{
    /**
     * Pull up the Guidelines edit page
     *
     * @throws AuthorizationException
     */
    public function edit(): Response
    {
        Gate::authorize('manageGuidelines', Auth::user()->currentTeam);

        return Inertia::render('Guidelines/Edit', [
            'guidelines' => Auth::user()->currentTeam->guidelines->load(['bullets', 'tickets']),
            'teams' => Auth::user()->allTeams()->reject(fn ($team) => $team->is(Auth::user()->currentTeam))
                ->pluck('name', 'id'),
        ]);
    }

    /**
     * Create a new guideline
     */
    public function create(CreateGuidelineRequest $request): RedirectResponse
    {
        $guideline = new Guideline($request->only(['score', 'description']));
        $guideline->team()->associate($request->user()->currentTeam);
        $guideline->save();

        if (! empty($request->bullets)) {
            $guideline->bullets()->createMany($request->bullets);
        }
        if (! empty($request->tickets)) {
            $guideline->tickets()->createMany($request->tickets);
        }

        return back()->with('status', 'guideline-created');
    }

    /**
     * Update the given guideline
     */
    public function update(Guideline $guideline, UpdateGuidelineRequest $request): RedirectResponse
    {
        $guideline->description = $request->description;
        $guideline->save();

        $bullets = collect($request->bullets);
        $guideline->bullets()->whereNotIn('id', $bullets->pluck('id')->filter())->delete();
        $guideline->bullets()
            ->saveMany(
                $bullets->map(fn ($bullet) => array_key_exists('id', $bullet) ?
                    GuidelineBullet::find($bullet['id'])->fill($bullet) :
                    new GuidelineBullet($bullet)
                ));

        $tickets = collect($request->tickets);
        $guideline->tickets()->whereNotIn('id', $tickets->pluck('id')->filter())->delete();
        $guideline->tickets()
            ->saveMany(
                $tickets->map(fn ($ticket) => array_key_exists('id', $ticket) ?
                    GuidelineTicket::find($ticket['id'])->fill($ticket) :
                    new GuidelineTicket($ticket)
                ));

        return back()->with('status', 'guideline-updated');
    }

    /**
     * Copy the guidelines from the team that is sent in the request
     *
     * @throws AuthorizationException
     */
    public function copy(Request $request): RedirectResponse
    {
        $otherTeam = Team::find($request->team);
        Gate::authorize('manageGuidelines', $otherTeam);

        $currentTeam = Auth::user()->currentTeam;
        $otherTeam->guidelines->each(function (Guideline $newGuideline) use ($currentTeam) {
            $oldGuideline = $currentTeam->guidelines()->whereScore($newGuideline->score)->first();
            if (empty($oldGuideline)) {
                $oldGuideline = $newGuideline->replicate();
                $oldGuideline->team()->associate($currentTeam);
            } else {
                $oldGuideline->description = $newGuideline->description;
                $oldGuideline->score = $newGuideline->score;
            }
            $oldGuideline->save();

            $newGuideline->tickets->each(fn (GuidelineTicket $transferTicket) => $transferTicket->replicate()->guideline()->associate($oldGuideline)->save()
            );

            $newGuideline->bullets->each(fn (GuidelineBullet $transferBullet) => $transferBullet->replicate()->guideline()->associate($oldGuideline)->save()
            );
        });

        return back();
    }
}
