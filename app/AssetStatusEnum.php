<?php

namespace App;

enum AssetStatusEnum: string
{
    case Deployed = 'Deployed';
    case InStorage = 'In Storage';
    case Maintenance = 'Maintenance';
    case Retired = 'Retired';
    case Broken = 'Broken';

    public function label(): string
    {
        return match ($this) {
            self::Deployed => 'Deployed',
            self::InStorage => 'In Storage',
            self::Maintenance => 'Maintenance',
            self::Retired => 'Retired',
            self::Broken => 'Broken',
        };
    }
}
