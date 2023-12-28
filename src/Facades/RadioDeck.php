<?php

namespace JaOcero\RadioDeck\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JaOcero\RadioDeck\RadioDeck
 */
class RadioDeck extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \JaOcero\RadioDeck\RadioDeck::class;
    }
}
