<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTeamMemberRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_member_roles_can_be_updated()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'scrum_master']
        );

        $response = $this->put('/teams/'.$user->currentTeam->id.'/members/'.$otherUser->id, [
            'role' => 'member',
        ]);

        $this->assertTrue($otherUser->fresh()->hasTeamRole(
            $user->currentTeam->fresh(), 'member'
        ));
    }

    public function test_only_team_owner_can_update_team_member_roles()
    {
        $user = User::factory()->withPersonalTeam()->create();

        $user->currentTeam->users()->attach(
            $otherUser = User::factory()->withPersonalTeam()->create(), ['role' => 'scrum_master']
        );

        $this->actingAs($otherUser);

        $response = $this->put('/teams/'.$user->currentTeam->id.'/members/'.$otherUser->id, [
            'role' => 'member',
        ]);

        $this->assertTrue($otherUser->fresh()->hasTeamRole(
            $user->currentTeam->fresh(), 'scrum_master'
        ));
    }
}
