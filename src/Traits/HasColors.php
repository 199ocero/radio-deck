<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;
use Filament\Support\Contracts\HasColor;
use Illuminate\Contracts\Support\Arrayable;
use UnitEnum;

trait HasColors
{
    /**
     * @var array<string | array<int | string, string | int> | null> | Arrayable | string | Closure | null
     */
    protected array|Arrayable|Closure|string|null $colors = null;

    /**
     * @var string | array<int | string, string | int> | Closure | null
     */
    protected string|array|Closure|null $defaultColor = 'primary';

    /**
     * @var array<string | array<int | string, string | int> | null> | string | null
     */
    protected array|string|null $cachedColors = null;

    protected bool $hasCachedColors = false;

    /**
     * Set the color of every option (`'success'`), a color per option
     * (`['ios' => 'info']`), or an enum class implementing `HasColor`.
     *
     * @param  array<string | array<int | string, string | int> | null> | Arrayable | string | Closure | null  $colors
     */
    public function colors(array|Arrayable|string|Closure|null $colors): static
    {
        $this->colors = $colors;
        $this->cachedColors = null;
        $this->hasCachedColors = false;

        return $this;
    }

    /**
     * @param  string | array<int | string, string | int> | Closure | null  $color
     */
    public function color(string|array|Closure|null $color): static
    {
        $this->defaultColor = $color;

        return $this;
    }

    /**
     * @return array<string | array<int | string, string | int> | null> | string | null
     */
    public function getColors(): array|string|null
    {
        if ($this->hasCachedColors) {
            return $this->cachedColors;
        }

        $colors = $this->evaluate($this->colors);

        if (is_string($colors) && enum_exists($colors)) {
            $colors = $this->getColorsFromEnum($colors);
        }

        if ($colors instanceof Arrayable) {
            $colors = $colors->toArray();
        }

        if (blank($colors) && filled($enum = $this->getEnum())) {
            $colors = $this->getColorsFromEnum($enum) ?: null;
        }

        $this->hasCachedColors = true;

        return $this->cachedColors = $colors;
    }

    /**
     * @param  class-string<UnitEnum>  $enum
     * @return array<string | array<int | string, string | int> | null>
     */
    protected function getColorsFromEnum(string $enum): array
    {
        if (! is_a($enum, HasColor::class, allow_string: true)) {
            return [];
        }

        return collect($enum::cases())
            ->mapWithKeys(fn (UnitEnum&HasColor $case): array => [
                ($case->value ?? $case->name) => $case->getColor(),
            ])
            ->all();
    }

    /**
     * @return string | array<int | string, string | int>
     */
    public function getColor(): string|array
    {
        return $this->evaluate($this->defaultColor) ?? 'primary';
    }

    /**
     * @param  array-key  $value
     * @return string | array<int | string, string | int>
     */
    public function getOptionColor($value): string|array
    {
        $colors = $this->getColors();

        if (is_string($colors)) {
            return $colors;
        }

        if (! is_array($colors)) {
            return $this->getColor();
        }

        return $colors[$value] ?? $this->getColor();
    }

    /**
     * @param  array-key  $value
     */
    public function hasOptionColor($value): bool
    {
        $colors = $this->getColors();

        if (! is_array($colors)) {
            return false;
        }

        return array_key_exists($value, $colors);
    }
}
