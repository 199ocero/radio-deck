# Radio Deck

![Header](https://raw.githubusercontent.com/199ocero/radio-deck/main/art/images/jaocero-radio-deck.jpeg)

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
                ->iconPosition(IconPosition::Before) // Before | After | (string - before | after)
                ->alignment(Alignment::Center) // Start | Center | End | (string - start | center | end)
                ->color('primary') // supports all color custom or not
                ->columns(3)
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
