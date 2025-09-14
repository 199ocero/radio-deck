<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

trait HasColors
{
    protected array|Arrayable|Closure|string|null $colors = null;

    protected string|Closure|null $defaultColor = 'primary';

    public function colors(array|Arrayable|string|Closure|null $colors): static
    {
        $this->colors = $colors;

        return $this;
    }

    public function color(string|Closure|null $color): static
    {
        $this->defaultColor = $color;

        return $this;
    }

    public function getColors(): mixed
    {
        $colors = $this->evaluate($this->colors);

        if ($colors instanceof Arrayable) {
            $colors = $colors->toArray();
        }

        return $colors;
    }

    public function getColor(): ?string
    {
        return $this->evaluate($this->defaultColor) ?? 'primary';
    }

    public function getOptionColor($value): ?string
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

    public function hasOptionColor($value): bool
    {
        $colors = $this->getColors();

        if (! is_array($colors)) {
            return false;
        }

        return array_key_exists($value, $colors);
    }
}
