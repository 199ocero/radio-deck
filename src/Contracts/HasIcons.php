<?php

namespace JaOcero\RadioDeck\Contracts;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;

interface HasIcons
{
    public function getIcons(): string | BackedEnum | Htmlable | null;
}
