<?php

namespace App\Http\Controllers;

use App\Events\CardChosen;
use App\Events\ResetCards;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EstimationController extends Controller
{

    public function index(Request $request)
    {
        $guidelines = $request->user()->currentTeam->guidelines()->orderBy('score')->with(['bullets', 'tickets'])->get();
        return Inertia::render('Estimation', [
            'team_id' => $request->user()->currentTeam->getKey(),
            'me' => $request->user()->only(['id', 'name', 'points']),
            'guidelines' => $guidelines
        ]);
    }

    public function runner(Request $request)
    {
        return Inertia::render('Runner', [
            'team_id' => $request->user()->currentTeam->getKey()
        ]);
    }

    public function choose(Request $request)
    {
        $request->validate(['points' => 'required']);

        $user = $request->user();
        $user->points = $request->points;
        $user->save();

        CardChosen::dispatch($request->user(), $request->points);

        return response()->noContent();
    }

    public function reset(Request $request, Team $team)
    {
        User::where('current_team_id', $team->getKey())->update(['points' => null]);

        ResetCards::dispatch($team->getKey());

        return response()->noContent();
    }

    public function resetUser(Request $request)
    {
        $user = $request->user();
        $user->points = null;
        $user->save();

        return response()->noContent();
    }
}
