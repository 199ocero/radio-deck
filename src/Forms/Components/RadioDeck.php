<?php

namespace JaOcero\RadioDeck\Forms\Components;

use Closure;
use Filament\Forms\Components\Radio;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasIcon;

class RadioDeck extends Radio
{
    use HasAlignment;
    use HasColor;
    use HasIcon;

    protected array|Closure|null $icons = null;

    protected string $view = 'radio-deck::forms.components.radio-deck';

    public function icons(array|Closure|null $icons): static
    {
        $this->icons = $icons;

        return $this;
    }

    /**
     * @param  array-key  $value
     */
    public function hasIcons($value): bool
    {
        if ($value && ! empty($this->getIcons())) {
            return array_key_exists($value, $this->getIcons());
        }

        return false;
    }

    /**
     * @return array | Closure | null
     */
    public function getIcons(): mixed
    {
        return $this->evaluate($this->icons);
    }

    public function getIcon($value): ?string
    {
        return $this->getIcons()[$value] ?? null;
    }
}
