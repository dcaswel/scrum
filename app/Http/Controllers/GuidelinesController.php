<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GuidelinesController extends Controller
{
    public function edit()
    {
        return Inertia::render('Guidelines/Edit', [
            'guidelines' => Auth::user()->currentTeam->guidelines->load(['bullets', 'tickets'])
        ]);
    }
}
