<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;

trait HasSelectionIndicator
{
    protected bool|Closure $hasSelectionIndicator = false;

    /**
     * Show a radio or checkbox style indicator in the corner of every card.
     */
    public function selectionIndicator(bool|Closure $condition = true): static
    {
        $this->hasSelectionIndicator = $condition;

        return $this;
    }

    public function hasSelectionIndicator(): bool
    {
        return (bool) $this->evaluate($this->hasSelectionIndicator);
    }
}
