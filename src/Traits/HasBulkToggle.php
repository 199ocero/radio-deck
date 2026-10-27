<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Enums\Size;

trait HasBulkToggle
{
    protected bool|Closure $isBulkToggleable = false;

    protected ?Closure $modifySelectAllActionUsing = null;

    protected ?Closure $modifyDeselectAllActionUsing = null;

    /**
     * Show "Select all" and "Deselect all" links. Only applies when `multiple()` is enabled.
     */
    public function bulkToggleable(bool|Closure $condition = true): static
    {
        $this->isBulkToggleable = $condition;

        return $this;
    }

    public function isBulkToggleable(): bool
    {
        return $this->isMultiple() && $this->evaluate($this->isBulkToggleable);
    }

    public function getSelectAllAction(): Action
    {
        $action = Action::make($this->getSelectAllActionName())
            ->label(__('filament-forms::components.checkbox_list.actions.select_all.label'))
            ->livewireClickHandlerEnabled(false)
            ->link()
            ->size(Size::Small);

        if ($this->modifySelectAllActionUsing) {
            $action = $this->evaluate($this->modifySelectAllActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function selectAllAction(?Closure $callback): static
    {
        $this->modifySelectAllActionUsing = $callback;

        return $this;
    }

    public function getSelectAllActionName(): string
    {
        return 'selectAll';
    }

    public function getDeselectAllAction(): Action
    {
        $action = Action::make($this->getDeselectAllActionName())
            ->label(__('filament-forms::components.checkbox_list.actions.deselect_all.label'))
            ->livewireClickHandlerEnabled(false)
            ->link()
            ->size(Size::Small);

        if ($this->modifyDeselectAllActionUsing) {
            $action = $this->evaluate($this->modifyDeselectAllActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function deselectAllAction(?Closure $callback): static
    {
        $this->modifyDeselectAllActionUsing = $callback;

        return $this;
    }

    public function getDeselectAllActionName(): string
    {
        return 'deselectAll';
    }
}
