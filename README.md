# Radio Deck

<div class="filament-hidden">
    
![Header](https://raw.githubusercontent.com/199ocero/radio-deck/main/art/images/jaocero-radio-deck.jpeg)

</div>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jaocero/radio-deck.svg?style=flat-square)](https://packagist.org/packages/jaocero/radio-deck)
[![Total Downloads](https://img.shields.io/packagist/dt/jaocero/radio-deck.svg?style=flat-square)](https://packagist.org/packages/jaocero/radio-deck)

Turn filament default radio button into a selectable card with icons, title and description.

## Requirements

- FilamentPHP v4.x
- PHP 8.2+
- Laravel v11.28+
- Tailwind CSS v4.0+

## Installation

You can install the package via composer:

```bash
composer require jaocero/radio-deck
```

### For FilamentPHP v4 Users

To adhere to Filament's theming approach, you'll be required to employ a personalized theme in order to utilize this plugin.

> **Custom Theme Installation**
> [Filament v4 Docs - Creating a Custom Theme](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme)

Instead of adding the plugin's views to your `tailwind.config.js` file, add the following source directive to your custom theme's CSS file (usually `resources/css/filament/admin/theme.css`):

```css
@source '../../../../vendor/jaocero/radio-deck/resources/views';
```

This will include the plugin's styles during the compilation process.

## Migration from v3 to v4

If you're upgrading from Radio Deck v3 to v4, please follow these steps:

### 1. Update Dependencies

```bash
composer require jaocero/radio-deck:^4.0
```

### 2. Create a Custom Theme

Since FilamentPHP v4 requires custom themes for plugins, you need to create one:

```bash
php artisan make:filament-theme
```

### 3. Update Theme Configuration

**Remove** the old configuration from your `tailwind.config.js`:

```js
// Remove this from tailwind.config.js
content: [
    ...
    './vendor/jaocero/radio-deck/resources/views/**/*.blade.php', // Remove this line
]
```

**Add** the source directive to your theme's CSS file instead:

```css
/* Add this to resources/css/filament/admin/theme.css */
@source '../../../../vendor/jaocero/radio-deck/resources/views';
```

### 4. Update Import Statements

Update your import statements to use the new namespace structure:

```php
// Old (v3)
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

// New (v4) - Same import, but make sure you're using v4
use JaOcero\RadioDeck\Forms\Components\RadioDeck;
```

### 5. Method Changes

Some method names have been updated for better consistency:

```php
// Old method
->optionsGap('gap-4') 

// New method (if you were using optionsGap)
->gap('gap-4') // Use the general gap method instead
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
                ->iconSizes([
                    'sm' => 'h-12 w-12',
                    'md' => 'h-14 w-14',
                    'lg' => 'h-16 w-16',
                ])
                ->iconPosition(IconPosition::Before) // Before | After
                ->alignment(Alignment::Center) // Start | Center | End
                ->gap('gap-5') // Gap between elements
                ->padding('px-4 py-6') // Padding around the deck
                ->extraCardsAttributes([ // Extra attributes for card elements
                    'class' => 'rounded-xl'
                ])
                ->extraOptionsAttributes([ // Extra attributes for option elements
                    'class' => 'text-3xl leading-none w-full flex flex-col items-center justify-center p-4'
                ])
                ->extraDescriptionsAttributes([ // Extra attributes for description elements
                    'class' => 'text-sm font-light text-center'
                ])
                ->color('primary') // Supports all Filament colors
                ->colors([ // Individual colors per option
                    'ios' => 'blue',
                    'android' => 'green',
                    'web' => 'purple',
                ])
                ->multiple() // Enable multiple selection (returns array)
                ->columns(3)
        ])
        ->columns('full');
}
```

### Using Enums

You can also utilize an Enum class for `->options()`, `->descriptions()`, and `->icons()`. Here's an example:

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

Usage with Enum:

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
                ->iconPosition(IconPosition::Before)
                ->alignment(Alignment::Center)
                ->color('danger')
                ->columns(3),
        ])
        ->columns('full');
}
```

## Available Methods

| Method | Description | Type |
|--------|-------------|------|
| `options()` | Set the available options | `array\|Enum\|Closure` |
| `descriptions()` | Set descriptions for options | `array\|Enum\|Closure` |
| `icons()` | Set icons for options | `array\|Enum\|Closure` |
| `multiple()` | Enable multiple selection | `bool\|Closure` |
| `color()` | Set default color | `string\|Closure` |
| `colors()` | Set individual colors per option | `array\|Closure` |
| `iconPosition()` | Set icon position (before/after) | `IconPosition\|string\|Closure` |
| `iconSizes()` | Set custom icon sizes | `array\|string\|IconSize\|Closure` |
| `alignment()` | Set content alignment | `Alignment\|string\|Closure` |
| `gap()` | Set gap between elements | `string\|Closure` |
| `padding()` | Set padding around cards | `string\|Closure` |
| `extraCardsAttributes()` | Add extra attributes to cards | `array\|Closure` |
| `extraOptionsAttributes()` | Add extra attributes to options | `array\|Closure` |
| `extraDescriptionsAttributes()` | Add extra attributes to descriptions | `array\|Closure` |

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