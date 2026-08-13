<?php

namespace App\Enums;

enum LabCaseStatus: string
{
    case Sent = 'sent';
    case InProgress = 'in_progress';
    case Received = 'received';
    case Delivered = 'delivered';

    public function label(): string
    {
        return match ($this) {
            self::Sent => __('dental_labs.status_sent'),
            self::InProgress => __('dental_labs.status_in_progress'),
            self::Received => __('dental_labs.status_received'),
            self::Delivered => __('dental_labs.status_delivered'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Sent => 'secondary',
            self::InProgress => 'warning',
            self::Received => 'info',
            self::Delivered => 'success',
        };
    }
}
