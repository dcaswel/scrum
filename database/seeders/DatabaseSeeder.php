<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->withPersonalTeam()
            ->has(Team::factory()->state(function (array $attributes, User $user) {
                return ['name' => 'Ctrl Alt Elite', 'user_id' => $user->id, 'personal_team' => false];
            }))
            ->create([
            'name' => 'Derek Caswell',
            'email' => 'dcaswell@goreact.com'
        ]);
        $team = Team::where('name', 'Ctrl Alt Elite')->first();
        User::factory(10)->withPersonalTeam()->hasAttached($team, ['role' => 'member'])->create();
    }
}
