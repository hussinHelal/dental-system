<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function create(User $user): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    /**
     * Covers both editing a payment record and adding a new
     * installment to it.
     */
    public function update(User $user, Payment $payment): bool
    {
        return $user->isDoctor() || $user->isReceptionist();
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->isDoctor();
    }
}
