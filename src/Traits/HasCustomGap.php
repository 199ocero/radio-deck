<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;

trait HasCustomGap
{
    protected string|Closure|null $customGap = null;

    public function customGap(string|Closure|null $gap): static
    {
        $this->gap = $gap;

        return $this;
    }

    public function getCustomGap(): ?string
    {
        return $this->evaluate($this->customGap);
    }
}
