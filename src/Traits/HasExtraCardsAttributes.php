<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraCardsAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraCardsAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraCardsAttributes(array|Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraCardsAttributes[] = $attributes;
        } else {
            $this->extraCardsAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraCardsAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag();

        foreach ($this->extraCardsAttributes as $extraCardsAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraCardsAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraCardsAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraCardsAttributes());
    }
}
