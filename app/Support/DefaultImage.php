<?php

namespace App\Support;

class DefaultImage
{
    public static function url(string $type): string
    {
        return asset('images/default-'.$type.'.svg');
    }

    public static function avatar(): string
    {
        return self::url('avatar');
    }

    public static function patient(): string
    {
        return self::url('patient');
    }

    public static function doctor(): string
    {
        return self::url('doctor');
    }

    public static function item(): string
    {
        return self::url('item');
    }

    public static function xray(): string
    {
        return self::url('xray');
    }
}
