<?php

namespace App\Http\Controllers;

use App\Enums\Points;
use App\Events\CardChosen;
use App\Events\ResetCards;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class EstimationController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        $guidelines = $request->user()->currentTeam->guidelines()->orderByRaw('CONVERT(score, SIGNED)')->with(['bullets', 'tickets'])->get();

        return Inertia::render('Estimation', [
            'team_id' => $request->user()->currentTeam->getKey(),
            'me' => $request->user()->only(['id', 'name', 'points']),
            'guidelines' => $guidelines,
        ]);
    }

    public function runner(Request $request): InertiaResponse
    {
        return Inertia::render('Runner', [
            'team_id' => $request->user()->currentTeam->getKey(),
        ]);
    }

    public function choose(Request $request): Response
    {
        $request->validate(['points' => ['required', Rule::in(Points::values())]]);

        $user = $request->user();
        $user->points = (string) $request->points;
        $user->save();

        CardChosen::dispatch($request->user(), $request->points);

        return response()->noContent();
    }

    public function reset(Request $request, Team $team): Response
    {
        User::where('current_team_id', $team->getKey())->update(['points' => null]);

        ResetCards::dispatch($team->getKey());

        return response()->noContent();
    }

    public function resetUser(Request $request): Response
    {
        $user = $request->user();
        $user->points = null;
        $user->save();

        return response()->noContent();
    }
}
