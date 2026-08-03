<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function view(User $user, Room $room): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function create(User $user): bool
    {
        return $user->isDoctor();
    }

    public function update(User $user, Room $room): bool
    {
        return $user->isDoctor();
    }

    public function delete(User $user, Room $room): bool
    {
        return $user->isDoctor();
    }
}
