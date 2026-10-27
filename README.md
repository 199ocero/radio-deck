# Radio Deck

<div class="filament-hidden">

![Header](https://raw.githubusercontent.com/199ocero/radio-deck/4.x/art/images/jaocero-radio-deck.jpeg)

</div>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jaocero/radio-deck.svg?style=flat-square)](https://packagist.org/packages/jaocero/radio-deck)
[![Total Downloads](https://img.shields.io/packagist/dt/jaocero/radio-deck.svg?style=flat-square)](https://packagist.org/packages/jaocero/radio-deck)

Turn Filament's radio and checkbox list into selectable cards with icons, titles, descriptions and colors.

## Requirements

- PHP 8.2+
- Laravel 11.28+
- Filament v4.x or v5.x
- A [custom Filament theme](https://filamentphp.com/docs/5.x/styling/overview#creating-a-custom-theme) (Tailwind CSS v4)

## Installation

```bash
composer require jaocero/radio-deck
```

Add the plugin's views to your custom theme's CSS file (usually `resources/css/filament/admin/theme.css`), then rebuild your assets:

```css
@source '../../../../vendor/jaocero/radio-deck/resources/views';
```

Publish the plugin's JavaScript (used by `searchable()` and `bulkToggleable()`):

```bash
php artisan filament:assets
```

## Usage

```php
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

public static function configure(Schema $schema): Schema
{
    return $schema
        ->components([
            RadioDeck::make('platform')
                ->options([
                    'ios' => 'iOS',
                    'android' => 'Android',
                    'web' => 'Web',
                ])
                ->descriptions([
                    'ios' => 'iOS mobile app',
                    'android' => 'Android mobile app',
                    'web' => 'Web app',
                ])
                ->icons([
                    'ios' => Heroicon::OutlinedDevicePhoneMobile,
                    'android' => 'heroicon-o-device-phone-mobile',
                    'web' => Heroicon::OutlinedGlobeAlt,
                ])
                ->required()
                ->iconSize(IconSize::Medium) // ExtraSmall | Small | Medium | Large | ExtraLarge | TwoExtraLarge, or classes like 'size-12'
                ->iconPosition(IconPosition::Before) // Before | After
                ->alignment(Alignment::Center) // Start | Center | End
                ->optionsGap('gap-5') // Gap between the icon and the text inside a card
                ->padding('px-4 py-6') // Padding inside each card
                ->extraCardsAttributes(['class' => 'rounded-xl'])
                ->extraOptionsAttributes(['class' => 'text-lg'])
                ->extraDescriptionsAttributes(['class' => 'text-sm font-light'])
                ->color('primary') // Color of every option
                ->columns(3), // or ['sm' => 1, 'md' => 2, 'lg' => 3]
        ]);
}
```

### Colors

Use one color for every option, or a color per option. Colors can be names registered with Filament or palettes from `Filament\Support\Colors\Color`:

```php
use Filament\Support\Colors\Color;

RadioDeck::make('plan')
    ->options([...])
    ->colors([
        'hobby' => 'gray',
        'pro' => Color::Violet,
        'team' => Color::hex('#0ea5e9'),
    ])
```

`color()` is only the fallback for options without their own color. A color from `colors()`, the options enum's `HasColor`, or `getOptionColorFromRecordUsing()` always wins. To force one color on every option, pass it to `colors()` instead:

```php
RadioDeck::make('platform')
    ->options(Platform::class)
    ->colors('danger') // Overrides the enum's colors
```

### Using enums

Pass an enum to `options()` and RadioDeck reads labels, descriptions, icons and colors from Filament's own enum contracts. The selected value is cast to the enum.

```php
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

enum Platform: string implements HasColor, HasDescription, HasIcon, HasLabel
{
    case Ios = 'ios';
    case Web = 'web';

    public function getLabel(): string
    {
        return match ($this) {
            self::Ios => 'iOS',
            self::Web => 'Web',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Ios => 'iOS mobile app',
            self::Web => 'Web app',
        };
    }

    public function getIcon(): Heroicon
    {
        return match ($this) {
            self::Ios => Heroicon::OutlinedDevicePhoneMobile,
            self::Web => Heroicon::OutlinedGlobeAlt,
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Ios => 'gray',
            self::Web => 'info',
        };
    }
}
```

```php
RadioDeck::make('platform')
    ->options(Platform::class)
```

Explicit `descriptions()`, `icons()` and `colors()` arrays still take priority over the enum.

> The `JaOcero\RadioDeck\Contracts\HasDescriptions` and `HasIcons` contracts (with `->descriptions(Enum::class)` and `->icons(Enum::class)`) still work, but are deprecated. Implement Filament's `HasDescription` and `HasIcon` instead.

### Multiple selection

`multiple()` renders checkboxes and stores an array. Cast the attribute to `array` (or an enum collection) on your model.

```php
RadioDeck::make('regions')
    ->options([...])
    ->multiple()
    ->minItems(1)
    ->maxItems(3)
```

### Searching and bulk toggling

```php
RadioDeck::make('regions')
    ->options([...])
    ->multiple()
    ->searchable()
    ->bulkToggleable() // "Select all" and "Deselect all", multiple() only
    ->searchPrompt('Search regions')
    ->noSearchResultsMessage('No regions found.')
    ->searchDebounce(300)
```

Customize the bulk toggle actions with `selectAllAction(fn (Action $action) => $action->label(...))` and `deselectAllAction()`.

### Integrating with an Eloquent relationship

For a `BelongsTo` relationship, name the field after the foreign key:

```php
use App\Models\Category;

RadioDeck::make('category_id')
    ->relationship('category', 'name')
    ->getOptionDescriptionFromRecordUsing(fn (Category $record): ?string => $record->description)
    ->getOptionIconFromRecordUsing(fn (Category $record): ?string => $record->icon)
    ->getOptionColorFromRecordUsing(fn (Category $record): ?string => $record->color)
```

For a `BelongsToMany` relationship, enable `multiple()`. The selection is synced to the pivot table, and `pivotData()` is saved with it:

```php
RadioDeck::make('features')
    ->relationship(titleAttribute: 'name')
    ->multiple()
    ->pivotData(['note' => 'Added by an admin'])
```

`relationship()` also accepts a `modifyQueryUsing` closure, and `getOptionLabelFromRecordUsing()` customizes the labels.

### Boolean options

```php
RadioDeck::make('is_public')
    ->boolean('Public', 'Private')
    ->icons([1 => Heroicon::OutlinedLockOpen, 0 => Heroicon::OutlinedLockClosed])
```

### Inline layout

`inline()` places the cards next to each other instead of in a grid:

```php
RadioDeck::make('size')
    ->options([...])
    ->inline()
```

In a grid, cards fill rows first. `gridDirection(GridDirection::Column)` fills columns first instead.

### Tooltips

```php
RadioDeck::make('plan')
    ->options([...])
    ->tooltips([
        'enterprise' => 'Contact sales to unlock this plan.',
    ])
```

### Selection indicator

Show a radio or checkbox style indicator in the corner of each card:

```php
RadioDeck::make('plan')
    ->options([...])
    ->selectionIndicator()
```

The indicator follows `alignment()`: vertically centered by default, at the top with `Alignment::Start` and at the bottom with `Alignment::End`. Stacked cards (with a `flex-col` class) keep it in the top corner. With the default padding, cards reserve space on the right for the indicator. If you set your own `padding()`, it's used as-is, so keep it symmetric for centered tiles.

### Disabling options

```php
RadioDeck::make('plan')
    ->options([...])
    ->disableOptionWhen(fn (string $value): bool => $value === 'enterprise')
```

Submitted values are validated against the enabled options automatically.

### Styling

Every part of the card has a stable CSS class you can target from your theme: `fi-fo-radio-deck`, `fi-fo-radio-deck-option`, `fi-fo-radio-deck-card`, `fi-fo-radio-deck-icon`, `fi-fo-radio-deck-label`, `fi-fo-radio-deck-description` and `fi-fo-radio-deck-indicator`.

## Upgrading

### From v1.x (Filament v3)

1. Create a custom theme with `php artisan make:filament-theme`.
2. Remove the plugin from the `content` array of your `tailwind.config.js` and add the `@source` directive shown above to your theme CSS.
3. Rename `->gap()` to `->optionsGap()`.

### Deprecated enum contracts

Replace `JaOcero\RadioDeck\Contracts\HasDescriptions::getDescriptions()` and `HasIcons::getIcons()` with Filament's `HasDescription::getDescription()` and `HasIcon::getIcon()`. You can then drop the `->descriptions(Enum::class)` and `->icons(Enum::class)` calls, because `->options(Enum::class)` picks them up.

## Testing

```bash
composer test
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
