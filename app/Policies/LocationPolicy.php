<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;

class LocationPolicy
{
    /**
     * Determine whether the user can view any locations.
     * Allowed: Admin and Operator.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    /**
     * Determine whether the user can view the location.
     * Allowed: Admin and Operator.
     */
    public function view(User $user, Location $location): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    /**
     * Determine whether the user can create locations.
     * Allowed: Admin only.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the location.
     * Allowed: Admin only.
     */
    public function update(User $user, Location $location): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the location.
     * Allowed: Admin only.
     */
    public function delete(User $user, Location $location): bool
    {
        return $user->isAdmin();
    }
}
