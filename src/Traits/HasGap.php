<?php

namespace JaOcero\RadioDeck\Traits;

trait HasGap
{
    protected string|Closure|null $gap = null;

    public function gap(string|Closure|null $gap): static
    {
        $this->gap = $gap;

        return $this;
    }

    public function getGap(): ?string
    {
        return $this->evaluate($this->gap);
    }
}
