<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function create(User $user): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->isDoctor();
    }
}
