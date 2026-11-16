<?php

namespace App\Policies;

use App\Models\Carrier;
use App\Models\User;

class CarrierPolicy
{
    /**
     * Determine whether the user can view any carriers.
     * Allowed: Admin and Operator.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    /**
     * Determine whether the user can view the carrier.
     * Allowed: Admin and Operator.
     */
    public function view(User $user, Carrier $carrier): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    /**
     * Determine whether the user can create carriers.
     * Allowed: Admin only.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the carrier.
     * Allowed: Admin only.
     */
    public function update(User $user, Carrier $carrier): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the carrier.
     * Allowed: Admin only.
     */
    public function delete(User $user, Carrier $carrier): bool
    {
        return $user->isAdmin();
    }
}
