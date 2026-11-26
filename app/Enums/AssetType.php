<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AssetType: string implements HasColor, HasLabel
{
    case Laptop = 'laptop';
    case PersonalEquipment = 'personal_equipment';
    case Server = 'server';

    public function getLabel(): string
    {
        return match ($this) {
            self::Laptop => 'Laptop',
            self::PersonalEquipment => 'Personal equipment',
            self::Server => 'Server',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Laptop => 'primary',
            self::PersonalEquipment => 'gray',
            self::Server => 'info',
        };
    }
}
