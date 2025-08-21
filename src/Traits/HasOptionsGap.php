<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;

trait HasOptionsGap
{
    protected string|Closure|null $optionsGap = null;

    public function optionsGap(string|Closure|null $gap): static
    {
        $this->optionsGap = $gap;

        return $this;
    }

    public function getOptionsGap(): ?string
    {
        return $this->evaluate($this->optionsGap);
    }
}
