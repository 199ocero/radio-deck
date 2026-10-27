<?php

namespace JaOcero\RadioDeck\Tests\Fixtures\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum Platform: string implements HasColor, HasDescription, HasIcon, HasLabel
{
    case Ios = 'ios';
    case Android = 'android';
    case Web = 'web';

    public function getLabel(): string
    {
        return match ($this) {
            self::Ios => 'iOS',
            self::Android => 'Android',
            self::Web => 'Web',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Ios => 'iOS mobile app',
            self::Android => 'Android mobile app',
            self::Web => 'Web app',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Ios, self::Android => Heroicon::DevicePhoneMobile,
            self::Web => Heroicon::GlobeAlt,
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Ios => 'info',
            self::Android => 'success',
            self::Web => 'warning',
        };
    }
}
