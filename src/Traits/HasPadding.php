<?php

namespace JaOcero\RadioDeck\Traits;

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
