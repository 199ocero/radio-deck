<?php

namespace JaOcero\RadioDeck\Commands;

use Illuminate\Console\Command;

class RadioDeckCommand extends Command
{
    public $signature = 'radio-deck';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
