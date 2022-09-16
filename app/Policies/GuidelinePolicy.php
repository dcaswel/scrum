<?php

namespace App\Policies;

use App\Models\Guideline;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuidelinePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Guideline $guideline): bool
    {
        return $user->hasTeamPermission($guideline->team, 'guideline:upsert');
    }
}
