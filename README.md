# Radio Deck

<div class="filament-hidden">
    
![Header](https://raw.githubusercontent.com/199ocero/radio-deck/main/art/images/jaocero-radio-deck.jpeg)

</div>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jaocero/radio-deck.svg?style=flat-square)](https://packagist.org/packages/jaocero/radio-deck)
[![Total Downloads](https://img.shields.io/packagist/dt/jaocero/radio-deck.svg?style=flat-square)](https://packagist.org/packages/jaocero/radio-deck)

Turn filament default radio button into a selectable card with icons, title and description.

## Installation

You can install the package via composer:

```bash
composer require jaocero/radio-deck
```

To adhere to Filament's theming approach, you'll be required to employ a personalized theme in order to utilize this plugin.

> **Custom Theme Installation**
> [Filament Docs](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme)

Add the plugin's views to your `tailwind.config.js` file.

```js
content: [
    ...
    './vendor/jaocero/radio-deck/resources/views/**/*.blade.php',
]
```

## Usage

```php
use JaOcero\RadioDeck\Forms\Components\RadioDeck;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            RadioDeck::make('name')
                ->options([
                    'ios' => 'iOS',
                    'android' => 'Android',
                    'web' => 'Web',
                    'windows' => 'Windows',
                    'mac' => 'Mac',
                    'linux' => 'Linux',
                ])
                ->descriptions([
                    'ios' => 'iOS Mobile App',
                    'android' => 'Android Mobile App',
                    'web' => 'Web App',
                    'windows' => 'Windows Desktop App',
                    'mac' => 'Mac Desktop App',
                    'linux' => 'Linux Desktop App',
                ])
                ->icons([
                    'ios' => 'heroicon-m-device-phone-mobile',
                    'android' => 'heroicon-m-device-phone-mobile',
                    'web' => 'heroicon-m-globe-alt',
                    'windows' => 'heroicon-m-computer-desktop',
                    'mac' => 'heroicon-m-computer-desktop',
                    'linux' => 'heroicon-m-computer-desktop',
                ])
                ->required()
                ->iconSize(IconSize::Large) // Small | Medium | Large | (string - sm | md | lg)
                ->iconSizes([ // Customize the values for each icon size
                    'sm' => 'h-12 w-12',
                    'md' => 'h-14 w-14',
                    'lg' => 'h-16 w-16',
                ])
                ->iconPosition(IconPosition::Before) // Before | After | (string - before | after)
                ->alignment(Alignment::Center) // Start | Center | End | (string - start | center | end)
                ->gap('gap-5') // Gap between Icon and Description (Any TailwindCSS gap-* utility)
                ->padding('px-4 px-6') // Padding around the deck (Any TailwindCSS padding utility)
                ->direction('column') // Column | Row (Allows to place the Icon on top)
                ->extraCardsAttributes([ // Extra Attributes to add to the card HTML element
                    'class' => 'rounded-xl'
                ])
                ->extraOptionsAttributes([ // Extra Attributes to add to the option HTML element
                    'class' => 'text-3xl leading-none w-full flex flex-col items-center justify-center p-4'
                ])
                ->extraDescriptionsAttributes([ // Extra Attributes to add to the description HTML element
                    'class' => 'text-sm font-light text-center'
                ])
                ->color('primary') // supports all color custom or not
                ->multiple() // Select multiple card (it will also returns an array of selected card values)
                ->columns(3)
        ])
        ->columns('full');
}
```
You can also utilize an Enum class for `->options()`, `->descriptions()`, and `->icons()` . Here's an example of how to create a simple enum class for this purpose:
```php
<?php

namespace App\Filament\Enums;

use Filament\Support\Contracts\HasLabel;
use JaOcero\RadioDeck\Contracts\HasDescriptions;
use JaOcero\RadioDeck\Contracts\HasIcons;

enum AssetType: string implements HasLabel, HasDescriptions, HasIcons
{
    case iOs = 'ios';
    case Android = 'android';
    case Web = 'web';
    case Windows = 'windows';
    case Mac = 'mac';
    case Linux = 'linux';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::iOs => 'iOS',
            self::Android => 'Android',
            self::Web => 'Web',
            self::Windows => 'Windows',
            self::Mac => 'Mac',
            self::Linux => 'Linux',
        };
    }

    public function getDescriptions(): ?string
    {
        return match ($this) {
            self::iOs => 'iOS Mobile App',
            self::Android => 'Android Mobile App',
            self::Web => 'Web App',
            self::Windows => 'Windows Desktop App',
            self::Mac => 'Mac Desktop App',
            self::Linux => 'Linux Desktop App',
        };
    }

    public function getIcons(): ?string
    {
        return match ($this) {
            self::iOs => 'heroicon-m-device-phone-mobile',
            self::Android => 'heroicon-m-device-phone-mobile',
            self::Web => 'heroicon-m-globe-alt',
            self::Windows => 'heroicon-m-computer-desktop',
            self::Mac => 'heroicon-m-computer-desktop',
            self::Linux => 'heroicon-m-computer-desktop',
        };
    }
}
```
After that, in your form, you can set it up like this:
```php
public static function form(Form $form): Form
{
    return $form
        ->schema([
            RadioDeck::make('name')
                ->options(AssetType::class)
                ->descriptions(AssetType::class)
                ->icons(AssetType::class)
                ->required()
                ->iconSize(IconSize::Large)
                ->iconPosition(IconPosition::Before)
                ->alignment(Alignment::Center)
                ->color('danger')
                ->columns(3),
        ])
        ->columns('full');
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jay-Are Ocero](https://github.com/199ocero)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
