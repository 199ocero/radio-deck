<?php

namespace JaOcero\RadioDeck\Tests\Fixtures\Livewire;

use Closure;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use JaOcero\RadioDeck\Tests\Fixtures\Models\Post;
use Livewire\Component;

class TestForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    /**
     * @var Closure(): array<\Filament\Schemas\Components\Component>
     */
    public static ?Closure $components = null;

    public ?Post $record = null;

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(?Post $record = null): void
    {
        $this->record = $record?->exists ? $record : null;

        $this->form->fill($this->record?->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components((static::$components)())
            ->model($this->record ?? Post::class)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if ($this->record) {
            $this->record->update($data);
        } else {
            $this->record = Post::create($data);
        }

        $this->form->model($this->record)->saveRelationships();
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->form }}
            </div>
            BLADE;
    }
}
