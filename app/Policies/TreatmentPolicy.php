<?php

namespace App\Policies;

use App\Models\Treatment;
use App\Models\User;

class TreatmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function view(User $user, Treatment $treatment): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function create(User $user): bool
    {
        return $user->isDoctor();
    }

    public function update(User $user, Treatment $treatment): bool
    {
        return $user->isDoctor();
    }

    public function delete(User $user, Treatment $treatment): bool
    {
        return $user->isDoctor();
    }
}
