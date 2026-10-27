<?php

namespace JaOcero\RadioDeck\Traits;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasSearch
{
    protected bool|Closure $isSearchable = false;

    protected string|Htmlable|Closure|null $searchPrompt = null;

    protected string|Htmlable|Closure|null $noSearchResultsMessage = null;

    protected int|Closure $searchDebounce = 0;

    public function searchable(bool|Closure $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
    }

    public function searchPrompt(string|Htmlable|Closure|null $message): static
    {
        $this->searchPrompt = $message;

        return $this;
    }

    public function getSearchPrompt(): string|Htmlable
    {
        return $this->evaluate($this->searchPrompt) ?? __('filament-forms::components.select.search_prompt');
    }

    public function noSearchResultsMessage(string|Htmlable|Closure|null $message): static
    {
        $this->noSearchResultsMessage = $message;

        return $this;
    }

    public function getNoSearchResultsMessage(): string|Htmlable
    {
        return $this->evaluate($this->noSearchResultsMessage) ?? __('filament-forms::components.select.no_search_results_message');
    }

    public function searchDebounce(int|Closure $debounce): static
    {
        $this->searchDebounce = $debounce;

        return $this;
    }

    public function getSearchDebounce(): int
    {
        return $this->evaluate($this->searchDebounce);
    }
}
