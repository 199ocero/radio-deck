<?php

namespace JaOcero\RadioDeck\Tests\Fixtures\Enums;

use Filament\Support\Contracts\HasLabel;
use JaOcero\RadioDeck\Contracts\HasDescriptions;
use JaOcero\RadioDeck\Contracts\HasIcons;

enum LegacyPlatform: string implements HasDescriptions, HasIcons, HasLabel
{
    case Windows = 'windows';
    case Linux = 'linux';

    public function getLabel(): string
    {
        return match ($this) {
            self::Windows => 'Windows',
            self::Linux => 'Linux',
        };
    }

    public function getDescriptions(): ?string
    {
        return match ($this) {
            self::Windows => 'Windows desktop app',
            self::Linux => 'Linux desktop app',
        };
    }

    public function getIcons(): ?string
    {
        return 'heroicon-o-computer-desktop';
    }
}
