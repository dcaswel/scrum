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
    public function run(): void
    {
        /** @var User $user */
        $user = User::factory()
            ->withPersonalTeam()
            ->create(['name' => 'Derek Caswell', 'email' => 'dcaswell@goreact.com']);
        Team::factory()->for($user, 'owner')
            ->hasAttached(User::factory(10)->withPersonalTeam(), ['role' => 'member'])
            ->create(['name' => 'Ctrl Alt Elite', 'personal_team' => false]);
    }
}
