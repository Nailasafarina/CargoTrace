<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    /**
     * Determine whether the user can view any shipments.
     * Allowed: All authenticated users (Admin & Operator see all, Customers see own shipments).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the shipment.
     * Allowed:
     * - Admin and Operator can view any shipment.
     * - Customer can ONLY view their own shipment ($user->id === $shipment->customer_id).
     */
    public function view(User $user, Shipment $shipment): bool
    {
        if ($user->isAdmin() || $user->isOperator()) {
            return true;
        }

        return $user->isCustomer() && $user->id === $shipment->customer_id;
    }

    /**
     * Determine whether the user can create shipments.
     * Allowed: Admin and Operator.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOperator();
    }

    /**
     * Determine whether the user can update the shipment.
     * Rule:
     * - Admin can update any shipment.
     * - Logistics Operator can only update shipments they personally created ($user->id === $shipment->created_by).
     * - Customer cannot update shipments.
     */
    public function update(User $user, Shipment $shipment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isOperator()) {
            return $user->id === $shipment->created_by;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the shipment.
     * Allowed: Admin only.
     */
    public function delete(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }
}
