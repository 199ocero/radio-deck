<?php

namespace JaOcero\RadioDeck\Traits;

use Illuminate\View\ComponentAttributeBag;

trait HasExtraCardAttributes
{
    /**
     * @var array<array<mixed> | Closure>
     */
    protected array $extraCardAttributes = [];

    /**
     * @param  array<mixed> | Closure  $attributes
     */
    public function extraCardAttributes(array|Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->extraCardAttributes[] = $attributes;
        } else {
            $this->extraCardAttributes = [$attributes];
        }

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getExtraCardAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag();

        foreach ($this->extraCardAttributes as $extraCardAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($extraCardAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraCardAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraCardAttributes());
    }
}
