<?php

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\User;

class InventoryItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function view(User $user, InventoryItem $item): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function create(User $user): bool
    {
        return $user->isDoctor();
    }

    /**
     * Both roles may reach the update endpoint, but
     * InventoryItemController strips every field except `quantity`
     * for a Receptionist before saving - new items, categories,
     * and thresholds stay Doctor-only.
     */
    public function update(User $user, InventoryItem $item): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function delete(User $user, InventoryItem $item): bool
    {
        return $user->isDoctor();
    }

    public function adjustThreshold(User $user): bool
    {
        return $user->isDoctor();
    }
}
