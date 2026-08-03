<?php

namespace App\Policies;

use App\Models\Backup;
use App\Models\User;

class BackupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function view(User $user, Backup $backup): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function download(User $user, Backup $backup): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function create(User $user): bool
    {
        return $user->isDoctor();
    }

    public function delete(User $user, Backup $backup): bool
    {
        return $user->isDoctor();
    }
}
