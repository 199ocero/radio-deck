<?php

namespace JaOcero\RadioDeck\Traits;

trait HasIconSizes
{
    protected array|Closure|null $iconSizes = [];

    public function iconSizes(array|Closure|null $iconSizes): static
    {
        $this->iconSizes = $iconSizes;

        return $this;
    }

    public function getIconSizes(string $size): ?string
    {
        return $this->evaluate($this->iconSizes[$size] ?? null);
    }
}
