<?php

namespace JaOcero\RadioDeck\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasIcon;
use Illuminate\Contracts\Support\Arrayable;
use JaOcero\RadioDeck\Contracts\HasDescriptions;
use JaOcero\RadioDeck\Contracts\HasIcons;
use JaOcero\RadioDeck\Intermediary\IntermediaryRadio;
use JaOcero\RadioDeck\Traits\HasDirection;
use JaOcero\RadioDeck\Traits\HasExtraDescriptionsAttributes;
use JaOcero\RadioDeck\Traits\HasExtraOptionsAttributes;
use JaOcero\RadioDeck\Traits\HasGap;
use JaOcero\RadioDeck\Traits\HasPadding;

class RadioDeck extends IntermediaryRadio
{
    use HasAlignment;
    use HasColor;
    use HasDirection;
    use HasExtraDescriptionsAttributes;
    use HasExtraOptionsAttributes;
    use HasGap;
    use HasIcon;
    use HasPadding;

    protected array|Arrayable|string|Closure|null $icons = null;

    protected array|Arrayable|string|Closure $descriptions = [];

    protected string $view = 'radio-deck::forms.components.radio-deck';

    public function icons(array|Arrayable|string|Closure|null $icons): static
    {
        $this->icons = $icons;

        return $this;
    }

    public function descriptions(array|Arrayable|string|Closure $descriptions): static
    {
        $this->descriptions = $descriptions;

        return $this;
    }

    /**
     * @param  array-key  $value
     */
    public function hasIcons($value): bool
    {
        if ($value !== null && ! empty($this->getIcons())) {
            return array_key_exists($value, $this->getIcons());
        }

        return false;
    }

    /**
     * @return array | Closure | null
     */
    public function getIcons(): mixed
    {
        $icons = $this->evaluate($this->icons);

        $enum = $icons;

        if (is_string($enum) && enum_exists($enum)) {
            if (is_a($enum, HasIcons::class, allow_string: true)) {
                return collect($enum::cases())
                    ->mapWithKeys(fn ($case) => [
                        ($case?->value ?? $case->name) => $case->getIcons() ?? $case->name,
                    ])
                    ->all();
            }

            return collect($enum::cases())
                ->mapWithKeys(fn ($case) => [
                    ($case?->value ?? $case->name) => $case->name,
                ])
                ->all();
        }

        if ($icons instanceof Arrayable) {
            $icons = $icons->toArray();
        }

        return $icons;
    }

    public function getIcon($value): ?string
    {
        return $this->getIcons()[$value] ?? null;
    }

    /**
     * @return array<string | Htmlable>
     */
    public function getDescriptions(): array
    {
        $descriptions = $this->evaluate($this->descriptions);

        $enum = $descriptions;

        if (is_string($enum) && enum_exists($enum)) {
            if (is_a($enum, HasDescriptions::class, allow_string: true)) {
                return collect($enum::cases())
                    ->mapWithKeys(fn ($case) => [
                        ($case?->value ?? $case->name) => $case->getDescriptions() ?? $case->name,
                    ])
                    ->all();
            }

            return collect($enum::cases())
                ->mapWithKeys(fn ($case) => [
                    ($case?->value ?? $case->name) => $case->name,
                ])
                ->all();
        }

        if ($descriptions instanceof Arrayable) {
            $descriptions = $descriptions->toArray();
        }

        return $descriptions;
    }
}
