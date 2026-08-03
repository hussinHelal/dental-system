<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDoctor();
    }

    public function view(User $user, User $target): bool
    {
        return $user->isDoctor();
    }

    public function create(User $user): bool
    {
        return $user->isDoctor();
    }

    public function update(User $user, User $target): bool
    {
        return $user->isDoctor();
    }

    /**
     * The primary Doctor/admin account can never be removed, even by
     * itself - deactivation of staff is soft (is_active flag) only.
     */
    public function delete(User $user, User $target): bool
    {
        return $user->isDoctor() && ! $target->isDoctor();
    }
}
