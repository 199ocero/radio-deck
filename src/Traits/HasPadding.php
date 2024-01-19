<?php

namespace JaOcero\RadioDeck\Traits;

use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;

trait HasPadding
{
    protected string|Closure|null $padding = null;

    public function padding(string|Closure|null $padding): static
    {
        $this->padding = $padding;

        return $this;
    }

    public function getPadding(): ?string
    {
        return $this->evaluate($this->padding);
    }

}