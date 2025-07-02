<?php

namespace JaOcero\RadioDeck\Tests;

use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;
use Livewire\Component;

class TestFormComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                RadioDeck::make('RadioDeck')
            ])
            ->statePath('data');
    }

    public function render(): string
    {
        return <<<'HTML'
        <div>
        {{ $this->form }}
        </div>
        HTML;
    }
}
