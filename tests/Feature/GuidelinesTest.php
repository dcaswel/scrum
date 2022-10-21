<?php


use App\Enums\Points;
use App\Http\Requests\CreateGuidelineRequest;
use App\Http\Requests\UpdateGuidelineRequest;
use App\Models\Guideline;
use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('The edit form can load', function () {
    $team = Team::factory()->create();
    $user = User::factory()->hasAttached($team, ['role' => 'member'])->withPersonalTeam()->create();

    $guidelines = Guideline::factory(2)->for($user->personalTeam())->hasTickets()->hasBullets()->create();
    login($user)->get('/guidelines/edit')
        ->assertInertia(fn(AssertableInertia $page) => $page
            ->component('Guidelines/Edit')
            ->has('guidelines', 2, fn(AssertableInertia $page) => $page
                ->where('description', $guidelines->first()->description)
                ->where('score', $guidelines->first()->score)
                ->has('tickets', 1)
                ->has('bullets', 1)
                ->etc()
            )
            ->has('teams', 1)
            ->has('teams', fn(AssertableInertia $page) => $page->where($team->getKey(), $team->name))
        );
});

test('A User must have permission to edit the team guidelines', function () {
    $team = Team::factory()->create();
    $user = User::factory()->withPersonalTeam()->hasAttached($team, ['role' => 'member'])->create();
    $user->switchTeam($team);

    login($user)->get(route('guidelines.edit'))->assertForbidden();
});

it('Can create a basic guideline with just a description', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $request = CreateGuidelineRequest::factory()->create();
    login($user)->from(route('guidelines.edit'))->post(route('guidelines.create'), $request)
        ->assertRedirect(route('guidelines.edit'));

    $this->assertDatabaseHas('guidelines', ['description' => $request['description'], 'score' => $request['score']]);
});

it('Can create a guideline with bullets', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $request = CreateGuidelineRequest::factory()->withNewBullets(1)->create();
    login($user)->from(route('guidelines.edit'))->post(route('guidelines.create'), $request)
        ->assertRedirect(route('guidelines.edit'));

    expect(Guideline::latest()->first())
        ->score->toBe($request['score'])
        ->bullets->toHaveCount(1)
        ->bullets->first()->body->toBe($request['bullets'][0]['body']);
});

it('Can create a guideline with tickets', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $request = CreateGuidelineRequest::factory()->withNewTickets(1)->create();
    login($user)->from(route('guidelines.edit'))->post(route('guidelines.create'), $request)
        ->assertRedirect(route('guidelines.edit'));

    expect(Guideline::latest()->first())
        ->score->toBe($request['score'])
        ->tickets->toHaveCount(1)
        ->tickets->first()->ticket_number->toBe($request['tickets'][0]['ticket_number']);
});

it('Cannot create a Guideline on the wrong team', function () {
    $team = Team::factory()->create();
    $user = User::factory()->withPersonalTeam()->hasAttached($team, ['role' => 'member'])->create();
    $user->switchTeam($team);
    login($user)->post(route('guidelines.create'))->assertForbidden();
})->fakeRequest(CreateGuidelineRequest::class);

it('Can Update a Guidelines description', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $guideline = Guideline::factory()->for($user->personalTeam())->create();
    $request = UpdateGuidelineRequest::factory()->create(['description' => 'Pick Me!!']);
    login($user)
        ->from(route('guidelines.edit'))
        ->put(route('guidelines.update', ['guideline' => $guideline]), $request)
        ->assertRedirect(route('guidelines.edit'));

    expect($guideline->fresh())
        ->description->toBe('Pick Me!!');
});

it('Can update existing bullets', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $guideline = Guideline::factory()->for($user->personalTeam())->hasBullets()->create();
    $request = UpdateGuidelineRequest::factory()->withBullet($guideline->bullets->first(), ['body' => 'I\'m a bullet'])
        ->create();
    login($user)
        ->from(route('guidelines.edit'))
        ->put(route('guidelines.update', $guideline), $request)
        ->assertRedirect(route('guidelines.edit'));

    expect($guideline->fresh())
        ->bullets->first()->body->toBe('I\'m a bullet');
});

it('Can add new bullets on update', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $guideline = Guideline::factory()->for($user->personalTeam())->create();
    expect($guideline->bullets)->toHaveCount(0);
    $request = UpdateGuidelineRequest::factory()->withNewBullet(['body' => 'You want a bullet'])->create();
    login($user)
        ->from(route('guidelines.edit'))
        ->put(route('guidelines.update', $guideline), $request)
        ->assertRedirect(route('guidelines.edit'));

    expect($guideline->fresh())
        ->bullets->toHaveCount(1)
        ->bullets->first()->body->toBe('You want a bullet');
});

it('Will remove any bullets that are not sent in an update', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $guideline = Guideline::factory()->for($user->personalTeam())->hasBullets()->create();
    expect($guideline->bullets)->toHaveCount(1);
    $request = UpdateGuidelineRequest::factory()->withNewBullet(['body' => 'I\'m a bullet'])
        ->create();
    login($user)
        ->from(route('guidelines.edit'))
        ->put(route('guidelines.update', $guideline), $request)
        ->assertRedirect(route('guidelines.edit'));

    expect($guideline->fresh())
        ->bullets->toHaveCount(1)
        ->bullets->first()->body->toBe('I\'m a bullet');
});

it('Can update a ticket number', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $guideline = Guideline::factory()->for($user->personalTeam())->hasTickets()->create();
    $request = UpdateGuidelineRequest::factory()->withTicket($guideline->tickets->first(), ['ticket_number' => 'CAE-5'])
        ->create();

    login($user)
        ->from(route('guidelines.edit'))
        ->put(route('guidelines.update', $guideline), $request)
        ->assertRedirect(route('guidelines.edit'));

    expect($guideline->fresh())
        ->tickets->toHaveCount(1)
        ->tickets->first()->ticket_number->toBe('CAE-5');
});

it('Can add a new ticket on update', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $guideline = Guideline::factory()->for($user->personalTeam())->create();
    expect($guideline->tickets)->toHaveCount(0);
    $request = UpdateGuidelineRequest::factory()->withNewTicket(['ticket_number' => 'CAE-5'])->create();

    login($user)
        ->from(route('guidelines.edit'))
        ->put(route('guidelines.update', $guideline), $request)
        ->assertRedirect(route('guidelines.edit'));

    expect($guideline->fresh())
        ->tickets->toHaveCount(1)
        ->tickets->first()->ticket_number->toBe('CAE-5');
});

it('Will remove tickets that are not sent in an update', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $guideline = Guideline::factory()->for($user->personalTeam())->hasTickets()->create();
    expect($guideline->tickets)->toHaveCount(1);
    $request = UpdateGuidelineRequest::factory()->withNewTicket(['ticket_number' => 'I\'m a ticket'])
        ->create();
    login($user)
        ->from(route('guidelines.edit'))
        ->put(route('guidelines.update', $guideline), $request)
        ->assertRedirect(route('guidelines.edit'));

    expect($guideline->fresh())
        ->tickets->toHaveCount(1)
        ->tickets->first()->ticket_number->toBe('I\'m a ticket');
});

test('A user cannot update a guideline without permission', function () {
    $team = Team::factory()->hasGuidelines()->create();
    $user = User::factory()->withPersonalTeam()->hasAttached($team, ['role' => 'member'])->create();

    login($user)->put(route('guidelines.update', $team->guidelines->first()))->assertForbidden();
})->fakeRequest(UpdateGuidelineRequest::class);

it('Can copy the guidelines from another team', function() {
    $user = User::factory()->withPersonalTeam()->create();
    Guideline::factory()->score(Points::Half)->for($user->personalTeam())->hasTickets()->create();
    $otherTeam = Team::factory()->hasAttached($user, ['role' => 'scrum_master'])->create();
    $halfGuideline = Guideline::factory()->score(Points::Half)->for($otherTeam)->hasTickets()->create();
    $oneGuideline = Guideline::factory()->score(Points::One)->for($otherTeam)->hasTickets()->hasBullets()->create();

    login($user)
        ->from(route('guidelines.edit'))
        ->post(route('guidelines.copy', ['team' => $otherTeam->getKey()]))
        ->assertRedirect(route('guidelines.edit'));

    expect($user->personalTeam())
        ->guidelines->toHaveCount(2)
        ->sequence(
            fn($guideline) => $guideline
                ->description->toBe($halfGuideline->description)
                ->tickets->toHaveCount(2)
                ->tickets->contains(fn($ticket) => $ticket->ticket_number === $halfGuideline->tickets->first()->ticket_number)->toBeTrue(),
            fn($guideline) => $guideline
                ->description->toBe($oneGuideline->description)
                ->tickets->toHaveCount(1)
                ->tickets->first()->ticket_number->toBe($oneGuideline->tickets->first()->ticket_number)
                ->bullets->toHaveCount(1)
                ->bullets->first()->body->toBe($oneGuideline->bullets->first()->body)
        );
});

test('User must have access to the team being copied', function() {
    $user = User::factory()->withPersonalTeam()->create();
    Guideline::factory()->score(Points::Half)->for($user->personalTeam())->hasTickets()->create();
    $otherTeam = Team::factory()->hasAttached($user, ['role' => 'member'])->create();

    login($user)->post(route('guidelines.copy', ['team' => $otherTeam->getKey()]))->assertForbidden();
});
