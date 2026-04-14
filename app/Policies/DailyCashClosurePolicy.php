<?php

namespace App\Policies;

use App\Models\DailyCashClosure;
use App\Models\User;

class DailyCashClosurePolicy
{
    public function viewAdminFields(User $user, DailyCashClosure $closure): bool
    {
        return $user->can('cash.close_admin');
    }

    public function reviewDifference(User $user, DailyCashClosure $closure): bool
    {
        return $user->can('cash.review_difference');
    }
}
