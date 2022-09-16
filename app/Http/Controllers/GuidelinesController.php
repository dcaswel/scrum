<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGuidelineRequest;
use App\Http\Requests\UpdateGuidelineRequest;
use App\Models\Guideline;
use App\Models\GuidelineBullet;
use App\Models\GuidelineTicket;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GuidelinesController extends Controller
{
    public function edit()
    {
        $this->authorize('manageGuidelines', Auth::user()->currentTeam);

        return Inertia::render('Guidelines/Edit', [
            'guidelines' => Auth::user()->currentTeam->guidelines->load(['bullets', 'tickets'])
        ]);
    }

    public function create(CreateGuidelineRequest $request)
    {
        $guideline = new Guideline($request->only(['score', 'description']));
        $guideline->team()->associate($request->user()->currentTeam);
        $guideline->save();

        if (!empty($request->bullets)) {
            $guideline->bullets()->createMany($request->bullets);
        }
        if (!empty($request->tickets)) {
            $guideline->tickets()->createMany($request->tickets);
        }

        return back()->with('status', 'guideline-created');
    }

    public function update(Guideline $guideline, UpdateGuidelineRequest $request)
    {
        $guideline->description = $request->description;
        $guideline->save();

        $bullets = collect($request->bullets);
        $guideline->bullets()->whereNotIn('id', $bullets->pluck('id')->filter())->delete();
        $guideline->bullets()
            ->saveMany(
                $bullets->map(fn($bullet) =>
                array_key_exists('id', $bullet) ?
                    GuidelineBullet::find($bullet['id'])->fill($bullet) :
                    new GuidelineBullet($bullet)
            ));

        $tickets = collect($request->tickets);
        $guideline->tickets()->whereNotIn('id', $tickets->pluck('id')->filter())->delete();
        $guideline->tickets()
            ->saveMany(
                $tickets->map(fn($ticket) =>
                array_key_exists('id', $ticket) ?
                    GuidelineTicket::find($ticket['id'])->fill($ticket) :
                    new GuidelineTicket($ticket)
                ));

        return back()->with('status', 'guideline-updated');
    }

}
