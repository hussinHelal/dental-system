<?php

namespace App\Enums;

enum PurchasePaymentStatus: string
{
    case Paid = 'paid';
    case Partial = 'partial';
    case Unpaid = 'unpaid';

    public function label(): string
    {
        return match ($this) {
            self::Paid => __('purchases.paid'),
            self::Partial => __('purchases.partial'),
            self::Unpaid => __('purchases.unpaid'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Paid => 'success',
            self::Partial => 'warning',
            self::Unpaid => 'danger',
        };
    }
}
