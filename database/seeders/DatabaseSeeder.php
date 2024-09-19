<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /** @var User $user */
        $user = User::factory()
            ->withPersonalTeam()
            ->create(['name' => 'Derek Caswell', 'email' => 'dcaswell@goreact.com']);
        $team = Team::factory()->for($user, 'owner')
            ->hasAttached(
                User::factory(10)
                    ->withPersonalTeam()
                    ->state(fn(array $attributes, Team $team) => ['current_team_id' => $team->id]),
                ['role' => 'member']
            )
            ->create(['name' => 'Copy, Paste, Repeat', 'personal_team' => false]);
        $user->currentTeam()->associate($team)->save();
    }
}
