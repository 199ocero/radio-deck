<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;
use Filament\Support\Enums\IconPosition;

trait HasIconPosition
{
    protected string|IconPosition|Closure|null $iconPosition = null;

    public function iconPosition(string|IconPosition|Closure|null $iconPosition): static
    {
        $this->iconPosition = $iconPosition;

        return $this;
    }

    public function getIconPosition(): ?string
    {
        $iconPosition = $this->evaluate($this->iconPosition);

        if ($iconPosition instanceof IconPosition) {
            return $iconPosition->value;
        }

        return $iconPosition;
    }
}
