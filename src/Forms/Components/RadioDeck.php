<?php

namespace JaOcero\RadioDeck\Forms\Components;

use Closure;
use Filament\Forms\Components\Radio;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasColor;

class RadioDeck extends Radio
{
    use HasColor;
    use HasIcon;
    use HasAlignment;

    protected array | Closure | null $icons = null;

    protected string $view = 'radio-deck::forms.components.radio-deck';

    public function icons(array | Closure | null $icons): static
    {
        $this->icons = $icons;

        return $this;
    }

    /**
     * @param  array-key  $value
     */
    public function hasIcons($value): bool
    {
        if ($value && !empty($this->getIcons())) {
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

    /**
     * @return string | null
     */
    public function getIcon($value): ?string
    {
        return $this->getIcons()[$value] ?? null;
    }
}
