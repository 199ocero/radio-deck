<?php

namespace JaOcero\RadioDeck\Forms\Components;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Concerns\CanLimitItemsLength;
use Filament\Forms\Components\Concerns\HasTooltips;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\StateCasts\EnumArrayStateCast;
use Filament\Schemas\Components\StateCasts\EnumStateCast;
use Filament\Schemas\Components\StateCasts\OptionsArrayStateCast;
use Filament\Schemas\Components\StateCasts\OptionStateCast;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Contracts\HasIcon as IconInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use JaOcero\RadioDeck\Contracts\HasIcons as LegacyHasIcons;
use JaOcero\RadioDeck\Intermediary\IntermediaryRadio;
use JaOcero\RadioDeck\Traits\HasBulkToggle;
use JaOcero\RadioDeck\Traits\HasColors;
use JaOcero\RadioDeck\Traits\HasExtraCardsAttributes;
use JaOcero\RadioDeck\Traits\HasExtraDescriptionsAttributes;
use JaOcero\RadioDeck\Traits\HasExtraOptionsAttributes;
use JaOcero\RadioDeck\Traits\HasIconPosition;
use JaOcero\RadioDeck\Traits\HasIconSize;
use JaOcero\RadioDeck\Traits\HasOptionsGap;
use JaOcero\RadioDeck\Traits\HasPadding;
use JaOcero\RadioDeck\Traits\HasRelationship;
use JaOcero\RadioDeck\Traits\HasSearch;
use JaOcero\RadioDeck\Traits\HasSelectionIndicator;
use UnitEnum;

class RadioDeck extends IntermediaryRadio
{
    use CanLimitItemsLength;
    use HasAlignment;
    use HasBulkToggle;
    use HasColors {
        getColors as getColorsWithoutRelationship;
    }
    use HasExtraCardsAttributes;
    use HasExtraDescriptionsAttributes;
    use HasExtraOptionsAttributes;
    use HasIcon;
    use HasIconPosition;
    use HasIconSize;
    use HasOptionsGap;
    use HasPadding;
    use HasRelationship;
    use HasSearch;
    use HasSelectionIndicator;
    use HasTooltips;

    /**
     * @var array<string | BackedEnum | Htmlable | null> | Arrayable | class-string<UnitEnum> | Closure | null
     */
    protected array|Arrayable|Closure|string|null $icons = null;

    /**
     * @var ?array<string | BackedEnum | Htmlable | null>
     */
    protected ?array $cachedIcons = null;

    protected bool|Closure $isMultiple = false;

    protected bool|Closure $isInline = false;

    protected string $view = 'radio-deck::forms.components.radio-deck';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(fn (RadioDeck $component): mixed => $component->isMultiple() ? [] : null);

        $this->afterStateHydrated(static function (RadioDeck $component, $state): void {
            if (! $component->isMultiple()) {
                return;
            }

            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });

        $this->registerActions([
            fn (RadioDeck $component): Action => $component->getSelectAllAction(),
            fn (RadioDeck $component): Action => $component->getDeselectAllAction(),
        ]);
    }

    /**
     * Set an icon per option, or pass an enum class implementing Filament's `HasIcon` contract.
     *
     * @param  array<string | BackedEnum | Htmlable | null> | Arrayable | class-string<UnitEnum> | Closure | null  $icons
     */
    public function icons(array|Arrayable|string|Closure|null $icons): static
    {
        $this->icons = $icons;
        $this->cachedIcons = null;

        return $this;
    }

    public function multiple(bool|Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function inline(bool|Closure $condition = true): static
    {
        $this->isInline = $condition;

        return $this;
    }

    public function isInline(): bool
    {
        return (bool) $this->evaluate($this->isInline);
    }

    public function boolean(?string $trueLabel = null, ?string $falseLabel = null): static
    {
        $this->options([
            1 => $trueLabel ?? __('filament-forms::components.radio.boolean.true'),
            0 => $falseLabel ?? __('filament-forms::components.radio.boolean.false'),
        ]);

        $this->stateCast(app(BooleanStateCast::class, ['isStoredAsInt' => true]));

        return $this;
    }

    public function hasNullableBooleanState(): bool
    {
        return true;
    }

    /**
     * @param  array-key  $value
     */
    public function hasIcons($value): bool
    {
        if ($value === null) {
            return false;
        }

        return filled($this->getIcon($value));
    }

    /**
     * Icons are resolved from an explicit array, an enum class passed to
     * `icons()`, the enum passed to `options()` when it implements Filament's
     * `HasIcon` contract, or `getOptionIconFromRecordUsing()`.
     *
     * @return array<string | BackedEnum | Htmlable | null>
     */
    public function getIcons(): array
    {
        if ($this->cachedIcons !== null) {
            return $this->cachedIcons;
        }

        if ($this->hasOptionIconFromRecordUsingCallback()) {
            return $this->cachedIcons = $this->mapRelationshipRecords(
                fn (Model $record): string|BackedEnum|Htmlable|null => $this->getOptionIconFromRecord($record),
            );
        }

        $icons = $this->evaluate($this->icons);

        if (is_string($icons) && enum_exists($icons)) {
            $icons = $this->getIconsFromEnum($icons);
        }

        if ($icons instanceof Arrayable) {
            $icons = $icons->toArray();
        }

        if (blank($icons) && filled($enum = $this->getEnum())) {
            $icons = $this->getIconsFromEnum($enum);
        }

        return $this->cachedIcons = $icons ?? [];
    }

    /**
     * @param  class-string<UnitEnum>  $enum
     * @return array<string | BackedEnum | Htmlable | null>
     */
    protected function getIconsFromEnum(string $enum): array
    {
        $isFilamentEnum = is_a($enum, IconInterface::class, allow_string: true);
        $isLegacyEnum = is_a($enum, LegacyHasIcons::class, allow_string: true);

        if ((! $isFilamentEnum) && (! $isLegacyEnum)) {
            return [];
        }

        return collect($enum::cases())
            ->mapWithKeys(fn (UnitEnum $case): array => [
                ($case->value ?? $case->name) => $isFilamentEnum ? $case->getIcon() : $case->getIcons(),
            ])
            ->all();
    }

    /**
     * @param  array-key  $value
     */
    public function getIcon($value = null): string|BackedEnum|Htmlable|null
    {
        if ($value === null) {
            return null;
        }

        return $this->getIcons()[$value] ?? null;
    }

    /**
     * @return array<string | Htmlable>
     */
    public function getDescriptions(): array
    {
        if (($this->cachedDescriptions === null) && $this->hasOptionDescriptionFromRecordUsingCallback()) {
            return $this->cachedDescriptions = $this->mapRelationshipRecords(
                fn (Model $record): string|Htmlable|null => $this->getOptionDescriptionFromRecord($record),
            );
        }

        return parent::getDescriptions();
    }

    /**
     * @return array<string | array<int | string, string | int> | null> | string | null
     */
    public function getColors(): array|string|null
    {
        if ((! $this->hasCachedColors) && $this->hasOptionColorFromRecordUsingCallback()) {
            $this->hasCachedColors = true;

            return $this->cachedColors = $this->mapRelationshipRecords(
                fn (Model $record): string|array|null => $this->getOptionColorFromRecord($record),
            );
        }

        return $this->getColorsWithoutRelationship();
    }

    public function getLabel(): string|Htmlable|null
    {
        if (($this->label === null) && $this->hasRelationship()) {
            $label = (string) str($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();

            return ($this->shouldTranslateLabel) ? __($label) : $label;
        }

        return parent::getLabel();
    }

    public function getEnumDefaultStateCast(): ?StateCast
    {
        $enum = $this->getEnum();

        if (blank($enum)) {
            return null;
        }

        return app(
            $this->isMultiple() ? EnumArrayStateCast::class : EnumStateCast::class,
            ['enum' => $enum],
        );
    }

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        if ($this->hasCustomStateCasts() || filled($this->getEnum())) {
            return parent::getDefaultStateCasts();
        }

        if ($this->isMultiple()) {
            return [app(OptionsArrayStateCast::class)];
        }

        return [app(OptionStateCast::class, ['isNullable' => true])];
    }

    /**
     * @return ?array<string>
     */
    public function getInValidationRuleValues(): ?array
    {
        $values = parent::getInValidationRuleValues();

        if ($values !== null) {
            return $values;
        }

        return array_keys($this->getEnabledOptions());
    }

    public function hasInValidationOnMultipleValues(): bool
    {
        return $this->isMultiple();
    }
}
