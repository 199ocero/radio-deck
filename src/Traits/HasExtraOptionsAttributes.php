<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraOptionsAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraOptionsAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraOptionsAttributes(array|Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraOptionsAttributes[] = $attributes;
        } else {
            $this->extraOptionsAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraOptionsAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraOptionsAttributes as $extraOptionsAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraOptionsAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraOptionsAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraOptionsAttributes());
    }
}
