<?php


use App\Models\Guideline;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('The edit form can load', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $guidelines = Guideline::factory(2)->for($user->personalTeam())->hasTickets()->hasBullets()->create();
    login($user)->get('/guidelines/edit')
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Guidelines/Edit')
            ->has('guidelines', 2, fn(AssertableInertia $page) => $page
                ->where('description', $guidelines->first()->description)
                ->where('score', number_format($guidelines->first()->score, decimals: 1))
                ->has('tickets', 1)
                ->has('bullets', 1)
                ->etc()
            )
        );
});
