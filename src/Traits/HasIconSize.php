<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;
use Filament\Support\Enums\IconSize;

trait HasIconSize
{
    protected string|IconSize|Closure|null $iconSize = null;

    /**
     * Define the size of the icon.
     *
     * @param  string|IconSize|Closure|null  $size  The icon size (e.g., IconSize::Medium, 'lg').
     */
    public function iconSize(string|IconSize|Closure|null $size): static
    {
        $this->iconSize = $size;

        return $this;
    }

    public function getIconSize(): string|IconSize|null
    {
        $size = $this->evaluate($this->iconSize);

        if (! is_string($size)) {
            return $size;
        }

        return IconSize::tryFrom($size) ?? $size;
    }
}
