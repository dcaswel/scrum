<?php

use App\Enums\Points;
use App\Events\CardChosen;
use App\Events\ResetCards;
use App\Models\Guideline;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia;

test('Showing the estimation page', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $user->switchTeam($user->personalTeam());
    $guidelines = Guideline::factory(2)->for($user->personalTeam())->hasBullets()->hasTickets()
        ->state(new Sequence(['score' => Points::One->value], ['score' => Points::Two->value]))
        ->create();
    login($user)->get(route('estimation'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Estimation')
            ->where('team_id', $user->personalTeam()->getKey())
            ->where('me', $user->only(['id', 'name', 'points']))
            ->has('guidelines', 2, fn (AssertableInertia $page) => $page
                ->where('id', $guidelines->first()->getKey())
                ->where('description', $guidelines->first()->description)
                ->where('score', $guidelines->first()->score)
                ->has('tickets', 1)
                ->has('bullets', 1)
                ->etc()
            )
        );
});

test('Showing the runner', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $user->switchTeam($user->personalTeam());
    login($user)->get(route('runner'))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Runner')
            ->has('team_id', $user->personalTeam()->getkey)
        );
});

test('A user can choose a card', function () {
    Event::fake();
    $user = User::factory()->withPersonalTeam()->create(['points' => Points::Two]);
    login($user)->post(route('choose'), ['points' => Points::One->value])->assertNoContent();
    expect($user->fresh())->points->toBe(Points::One);
    Event::assertDispatched(fn (CardChosen $event) => $event->user->is($user) && $event->points === Points::One->value);
});

test('A user must supply points for the card they chose', function () {
    $user = User::factory()->withPersonalTeam()->create();
    login($user)->post(route('choose'), [])->assertStatus(302)
        ->assertSessionHasErrors(['points' => 'The points field is required.']);
});

test('The points a user gives must be valid points', function () {
    $user = User::factory()->withPersonalTeam()->create();
    login($user)->post(route('choose'), ['points' => 'One Million'])->assertStatus(302)
        ->assertSessionHasErrors(['points' => 'The selected points is invalid.']);
});

it('Can reset the scores', function () {
    Event::fake();
    $user = User::factory()->withPersonalTeam()->create(['points' => Points::One]);
    $team = $user->personalTeam();
    $user->switchTeam($team);
    $user2 = User::factory()->hasAttached($team, ['role' => 'member'])->create(['points' => Points::Two]);
    $user2->switchTeam($team);

    login($user)->delete(route('reset', ['team' => $team]))->assertNoContent();

    Event::assertDispatched(fn (ResetCards $event) => $event->teamId === $team->getKey());

    expect($user->fresh())->points->toBeNull()
        ->and($user2->fresh())->points->toBeNull();
});

it('Can reset a single user', function () {
    $user = User::factory()->withPersonalTeam()->create(['points' => Points::One]);

    login($user)->patch(route('reset-user'))->assertNoContent();

    expect($user->fresh())->points->toBeNull();
});
