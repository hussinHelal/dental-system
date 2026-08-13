<?php

namespace App\Enums;

enum InsuranceStatus: string
{
    case Active = 'active';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Active => __('insurance.status_active'),
            self::Expired => __('insurance.status_expired'),
            self::Cancelled => __('insurance.status_cancelled'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Expired => 'secondary',
            self::Cancelled => 'danger',
        };
    }
}
