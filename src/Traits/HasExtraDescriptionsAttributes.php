<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;
use Illuminate\View\ComponentAttributeBag;

trait HasExtraDescriptionsAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraDescriptionsAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraDescriptionsAttributes(array|Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraDescriptionsAttributes[] = $attributes;
        } else {
            $this->extraDescriptionsAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraDescriptionsAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->extraDescriptionsAttributes as $extraDescriptionsAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraDescriptionsAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraDescriptionsAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraDescriptionsAttributes());
    }
}
