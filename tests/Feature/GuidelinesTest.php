<?php


use App\Http\Requests\CreateGuidelineRequest;
use App\Http\Requests\UpdateGuidelineRequest;
use App\Models\Guideline;
use App\Models\Team;
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
    $guideline = Guideline::factory()->create();
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
