<?php

namespace JaOcero\RadioDeck\Intermediary;

use Closure;
use Filament\Forms\Components\Concerns\CanDisableOptions as ConcernCanDisableOptions;
use Filament\Forms\Components\Concerns\CanDisableOptionsWhenSelectedInSiblingRepeaterItems;
use Filament\Forms\Components\Concerns\CanFixIndistinctState;
use Filament\Forms\Components\Concerns\HasExtraInputAttributes;
use Filament\Forms\Components\Concerns\HasGridDirection;
use Filament\Forms\Components\Concerns\HasOptions;
use Filament\Forms\Components\Contracts\CanDisableOptions as ContractsCanDisableOptions;
use Filament\Forms\Components\Field;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;

class IntermediaryRadio extends Field implements ContractsCanDisableOptions
{
    use CanDisableOptionsWhenSelectedInSiblingRepeaterItems;
    use CanFixIndistinctState;
    use ConcernCanDisableOptions;
    use HasExtraInputAttributes;
    use HasGridDirection;
    use HasOptions;

    protected Arrayable|Closure|string $descriptions = [];

    /**
     * @param  array<string | Htmlable> | Arrayable | string | Closure  $descriptions
     */
    public function descriptions(array|Arrayable|string|Closure $descriptions): static
    {
        $this->descriptions = $descriptions;

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
     * @return array<string | Htmlable>
     */
    public function getDescriptions(): array
    {
        $descriptions = $this->evaluate($this->descriptions);

        if ($descriptions instanceof Arrayable) {
            $descriptions = $descriptions->toArray();
        }

        return $descriptions;
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
