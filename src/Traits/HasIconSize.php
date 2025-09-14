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
     * @param  string|IconSize|Closure|null  $size  The icon size (e.g., IconSize::Medium, 'lg', 'h-12 w-12').
     */
    public function iconSize(string|IconSize|Closure|null $size): static
    {
        $this->iconSize = $size;

        return $this;
    }

    public function getIconSize(): ?string
    {
        $size = $this->evaluate($this->iconSize);

        if ($size instanceof IconSize) {
            return $size->value;
        }

        return $size;
    }
}
