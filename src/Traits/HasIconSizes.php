<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;
use Filament\Support\Enums\IconSize;

trait HasIconSizes
{
    protected array|string|IconSize|Closure|null $iconSizes = [];

    public function iconSizes(array|string|IconSize|Closure|null $iconSizes): static
    {
        $this->iconSizes = $iconSizes;

        return $this;
    }

    public function getIconSizes(string $size): ?string
    {
        $iconSizes = $this->evaluate($this->iconSizes);

        if ($iconSizes instanceof IconSize) {
            return $iconSizes->value;
        }

        if (is_string($iconSizes)) {
            return $iconSizes;
        }

        if (is_array($iconSizes) && isset($iconSizes[$size])) {
            $sizeValue = $iconSizes[$size];

            if ($sizeValue instanceof IconSize) {
                return $sizeValue->value;
            }

            return $sizeValue;
        }

        return null;
    }
}
