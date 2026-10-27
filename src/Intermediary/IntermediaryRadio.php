<?php

namespace JaOcero\RadioDeck\Intermediary;

use Closure;
use Filament\Forms\Components\Concerns\CanDisableOptions as ConcernCanDisableOptions;
use Filament\Forms\Components\Concerns\CanDisableOptionsWhenSelectedInSiblingRepeaterItems;
use Filament\Forms\Components\Concerns\CanFixIndistinctState;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Concerns\HasGridDirection;
use Filament\Forms\Components\Concerns\HasNestedRecursiveValidationRules;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Contracts\CanDisableOptions as ContractsCanDisableOptions;
use Filament\Forms\Components\Contracts\HasNestedRecursiveValidationRules as ContractsHasNestedRecursiveValidationRules;
use Filament\Forms\Components\Field;
use Filament\Support\Contracts\HasDescription;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use JaOcero\RadioDeck\Contracts\HasDescriptions as LegacyHasDescriptions;
use UnitEnum;

class IntermediaryRadio extends Field implements ContractsCanDisableOptions, ContractsHasNestedRecursiveValidationRules
{
    use CanDisableOptionsWhenSelectedInSiblingRepeaterItems;
    use CanFixIndistinctState;
    use ConcernCanDisableOptions;
    use HasExtraInputAttributes;
    use HasGridDirection;
    use HasNestedRecursiveValidationRules;
    use HasOptions;

    /**
     * @var array<string | Htmlable> | Arrayable | class-string<UnitEnum> | Closure
     */
    protected array|Arrayable|Closure|string $descriptions = [];

    /**
     * @var ?array<string | Htmlable>
     */
    protected ?array $cachedDescriptions = null;

    /**
     * @param  array<string | Htmlable> | Arrayable | class-string<UnitEnum> | Closure  $descriptions
     */
    public function descriptions(array|Arrayable|string|Closure $descriptions): static
    {
        $this->descriptions = $descriptions;
        $this->cachedDescriptions = null;

        return $this;
    }

    /**
     * @param  array-key  $value
     */
    public function hasDescription($value): bool
    {
        return array_key_exists($value, $this->getDescriptions());
    }

    /**
     * @param  array-key  $value
     */
    public function getDescription($value): string|Htmlable|null
    {
        return $this->getDescriptions()[$value] ?? null;
    }

    /**
     * Descriptions are resolved from an explicit array, an enum class passed to
     * `descriptions()`, or the enum passed to `options()` when it implements
     * Filament's `HasDescription` contract.
     *
     * @return array<string | Htmlable>
     */
    public function getDescriptions(): array
    {
        if ($this->cachedDescriptions !== null) {
            return $this->cachedDescriptions;
        }

        $descriptions = $this->evaluate($this->descriptions);

        if (is_string($descriptions) && enum_exists($descriptions)) {
            $descriptions = $this->getDescriptionsFromEnum($descriptions, fallbackToCaseName: true);
        }

        if ($descriptions instanceof Arrayable) {
            $descriptions = $descriptions->toArray();
        }

        if (blank($descriptions) && filled($enum = $this->getEnum())) {
            $descriptions = $this->getDescriptionsFromEnum($enum);
        }

        return $this->cachedDescriptions = $descriptions ?? [];
    }

    /**
     * @param  class-string<UnitEnum>  $enum
     * @return array<string | Htmlable>
     */
    protected function getDescriptionsFromEnum(string $enum, bool $fallbackToCaseName = false): array
    {
        $isFilamentEnum = is_a($enum, HasDescription::class, allow_string: true);
        $isLegacyEnum = is_a($enum, LegacyHasDescriptions::class, allow_string: true);

        if ((! $isFilamentEnum) && (! $isLegacyEnum) && (! $fallbackToCaseName)) {
            return [];
        }

        return collect($enum::cases())
            ->mapWithKeys(fn (UnitEnum $case): array => [
                ($case->value ?? $case->name) => match (true) {
                    $isFilamentEnum => $case->getDescription(),
                    $isLegacyEnum => $case->getDescriptions() ?? $case->name,
                    default => $case->name,
                },
            ])
            ->filter(fn (mixed $description): bool => filled($description))
            ->all();
    }

    public function getDefaultState(): mixed
    {
        $state = parent::getDefaultState();

        if (is_bool($state)) {
            return $state ? 1 : 0;
        }

        return $state;
    }
}
