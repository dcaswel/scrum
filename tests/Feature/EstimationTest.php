<?php


use App\Events\CardChosen;
use App\Models\Guideline;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia;

test('Showing the estimation page', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $guidelines = Guideline::factory(2)->for($user->personalTeam())->hasBullets()->hasTickets()
        ->state(new Sequence(['score' => 1], ['score' => 2]))
        ->create();
    login($user)->get(route('estimation'))
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Estimation')
            ->where('team_id', $user->personalTeam()->getKey())
            ->where('me', $user->only(['id', 'name', 'points']))
            ->has('guidelines', 2, fn(AssertableInertia $page) => $page
                ->where('id', $guidelines->first()->getKey())
                ->where('description', $guidelines->first()->description)
                ->where('score', number_format($guidelines->first()->score, decimals: 1))
                ->has('tickets', 1)
                ->has('bullets', 1)
                ->etc()
            )
        );
});

test('Showing the runner', function () {
    $user = User::factory()->withPersonalTeam()->create();
    login($user)->get(route('runner'))
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Runner')
            ->has('team_id', $user->personalTeam()->getkey)
        );
});

test('A user can choose a card', function () {
    Event::fake();
    $user = User::factory()->withPersonalTeam()->create(['points' => 2]);
    login($user)->post(route('choose'), ['points' => '1.0'])->assertNoContent();
    expect($user->fresh())->points->toBe('1.0');
    Event::assertDispatched(fn(CardChosen $event) => $event->user->is($user) && $event->points === '1.0');
});

test('A user must supply points for the card they chose');

test('The points a user gives must be valid points');

it('Can reset the scores');

it('Can reset a single user');
