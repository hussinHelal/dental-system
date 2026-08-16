<?php

namespace App\Enums;

enum ToothStatus: string
{
    case Healthy = 'healthy';
    case Caries = 'caries';
    case Filled = 'filled';
    case Crown = 'crown';
    case RootCanal = 'root_canal';
    case Missing = 'missing';
    case Bridge = 'bridge';
    case Implant = 'implant';
    case Impacted = 'impacted';

    public function label(): string
    {
        return match ($this) {
            self::Healthy => __('tooth_chart.status_healthy'),
            self::Caries => __('tooth_chart.status_caries'),
            self::Filled => __('tooth_chart.status_filled'),
            self::Crown => __('tooth_chart.status_crown'),
            self::RootCanal => __('tooth_chart.status_root_canal'),
            self::Missing => __('tooth_chart.status_missing'),
            self::Bridge => __('tooth_chart.status_bridge'),
            self::Implant => __('tooth_chart.status_implant'),
            self::Impacted => __('tooth_chart.status_impacted'),
        };
    }

    /** Fill color used for the tooth shape in the SVG chart. */
    public function color(): string
    {
        return match ($this) {
            self::Healthy => '#ffffff',
            self::Caries => '#dc3545',
            self::Filled => '#0d6efd',
            self::Crown => '#d4a017',
            self::RootCanal => '#6f42c1',
            self::Missing => '#adb5bd',
            self::Bridge => '#fd7e14',
            self::Implant => '#20c997',
            self::Impacted => '#842029',
        };
    }

    /** All cases, for building the legend / select options. */
    public static function options(): array
    {
        return array_map(
            fn (self $status) => ['value' => $status->value, 'label' => $status->label(), 'color' => $status->color()],
            self::cases()
        );
    }
}
