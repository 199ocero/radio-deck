<?php

namespace JaOcero\RadioDeck\Traits;

use BackedEnum;
use Closure;
use Filament\Forms\Components\Concerns\HasPivotData;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;
use LogicException;

/**
 * Loads options from a `BelongsTo` relationship (single selection, the field
 * name is the foreign key) or a `BelongsToMany` relationship (multiple
 * selection, synced to the pivot table on save).
 */
trait HasRelationship
{
    use HasPivotData;

    protected string|Closure|null $relationship = null;

    protected string|Closure|null $relationshipTitleAttribute = null;

    protected ?Closure $modifyRelationshipQueryUsing = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected ?Closure $getOptionDescriptionFromRecordUsing = null;

    protected ?Closure $getOptionIconFromRecordUsing = null;

    protected ?Closure $getOptionColorFromRecordUsing = null;

    protected ?Collection $cachedRelationshipRecords = null;

    public function relationship(string|Closure|null $name = null, string|Closure|null $titleAttribute = null, ?Closure $modifyQueryUsing = null): static
    {
        $this->relationship = $name ?? $this->getName();
        $this->relationshipTitleAttribute = $titleAttribute;
        $this->modifyRelationshipQueryUsing = $modifyQueryUsing;
        $this->cachedRelationshipRecords = null;

        $this->options(static function (RadioDeck $component): array {
            $keyName = $component->getRelationshipRelatedKeyName();
            $titleAttribute = $component->getRelationshipTitleAttribute();

            return $component->getRelationshipRecords()
                ->mapWithKeys(static fn (Model $record): array => [
                    $record->getAttribute($keyName) => $component->hasOptionLabelFromRecordUsingCallback()
                        ? $component->getOptionLabelFromRecord($record)
                        : $record->getAttribute($titleAttribute),
                ])
                ->all();
        });

        $this->loadStateFromRelationshipsUsing(static function (RadioDeck $component): void {
            if (! $component->getRelationship() instanceof BelongsToMany) {
                return;
            }

            $component->fillStateFromRelationship();
        });

        $this->saveRelationshipsUsing(static function (RadioDeck $component): void {
            if (! $component->getRelationship() instanceof BelongsToMany) {
                return;
            }

            $component->saveStateToRelationship();
        });

        $this->dehydrated(static fn (RadioDeck $component): bool => ! $component->getRelationship() instanceof BelongsToMany);

        return $this;
    }

    public function hasRelationship(): bool
    {
        return filled($this->getRelationshipName());
    }

    public function getRelationship(): BelongsTo|BelongsToMany|null
    {
        $name = $this->getRelationshipName();

        if (blank($name)) {
            return null;
        }

        $record = $this->getModelInstance();

        if ($record->hasAttribute($name) || (! $record->isRelation($name))) {
            throw new LogicException("The relationship [{$name}] does not exist on the model [{$this->getModel()}].");
        }

        $relationship = $record->{$name}();

        if ((! $relationship instanceof BelongsTo) && (! $relationship instanceof BelongsToMany)) {
            throw new LogicException("The relationship [{$name}] on the model [{$this->getModel()}] must be a BelongsTo or BelongsToMany relationship.");
        }

        return $relationship;
    }

    public function getRelationshipName(): ?string
    {
        return $this->evaluate($this->relationship);
    }

    public function getRelationshipTitleAttribute(): ?string
    {
        return $this->evaluate($this->relationshipTitleAttribute);
    }

    public function getRelationshipRelatedKeyName(): string
    {
        $relationship = $this->getRelationship();

        return $relationship instanceof BelongsToMany
            ? $relationship->getRelatedKeyName()
            : $relationship->getOwnerKeyName();
    }

    public function getRelationshipRecords(): Collection
    {
        if ($this->cachedRelationshipRecords !== null) {
            return $this->cachedRelationshipRecords;
        }

        $relationship = Relation::noConstraints(fn () => $this->getRelationship());

        $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

        if ($this->modifyRelationshipQueryUsing) {
            $relationshipQuery = $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationshipQuery,
            ]) ?? $relationshipQuery;
        }

        $titleAttribute = $this->getRelationshipTitleAttribute();

        if (empty($relationshipQuery->getQuery()->orders) && filled($titleAttribute)) {
            $relationshipQuery->orderBy($relationshipQuery->qualifyColumn($titleAttribute));
        }

        return $this->cachedRelationshipRecords = $relationshipQuery->get();
    }

    public function fillStateFromRelationship(): void
    {
        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        if ($this->modifyRelationshipQueryUsing) {
            $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationship->getQuery(),
            ]);
        }

        // Cast the related keys to a string, otherwise Livewire does not
        // know how to handle deselection.
        $this->state(
            $relationship->getResults()
                ->pluck($relationship->getRelatedKeyName())
                ->map(static fn ($key): string => strval($key))
                ->all(),
        );
    }

    public function saveStateToRelationship(): void
    {
        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();
        $record = $this->getRecord();

        if ($this->modifyRelationshipQueryUsing) {
            $this->evaluate($this->modifyRelationshipQueryUsing, [
                'query' => $relationship->getQuery(),
            ]);
        }

        $state = array_map(strval(...), $this->getState() ?? []);

        $recordsToDetach = array_diff(
            $relationship->getResults()
                ->pluck($relationship->getRelatedKeyName())
                ->map(static fn ($key): string => strval($key))
                ->all(),
            $state,
        );

        if (count($recordsToDetach) > 0) {
            $relationship->detach($recordsToDetach);
        }

        $pivotData = $this->getPivotData();

        if ($pivotData === []) {
            $relationship->sync($state, detaching: false);
        } else {
            $relationship->syncWithPivotValues($state, $pivotData, detaching: false);
        }

        $record->unsetRelation($this->getRelationshipName());
    }

    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionLabelFromRecordUsing = $callback;

        return $this;
    }

    public function hasOptionLabelFromRecordUsingCallback(): bool
    {
        return $this->getOptionLabelFromRecordUsing !== null;
    }

    public function getOptionLabelFromRecord(Model $record): string|Htmlable
    {
        return $this->evaluateWithRecord($this->getOptionLabelFromRecordUsing, $record);
    }

    public function getOptionDescriptionFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionDescriptionFromRecordUsing = $callback;
        $this->cachedDescriptions = null;

        return $this;
    }

    public function hasOptionDescriptionFromRecordUsingCallback(): bool
    {
        return $this->hasRelationship() && ($this->getOptionDescriptionFromRecordUsing !== null);
    }

    public function getOptionDescriptionFromRecord(Model $record): string|Htmlable|null
    {
        return $this->evaluateWithRecord($this->getOptionDescriptionFromRecordUsing, $record);
    }

    public function getOptionIconFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionIconFromRecordUsing = $callback;
        $this->cachedIcons = null;

        return $this;
    }

    public function hasOptionIconFromRecordUsingCallback(): bool
    {
        return $this->hasRelationship() && ($this->getOptionIconFromRecordUsing !== null);
    }

    public function getOptionIconFromRecord(Model $record): string|BackedEnum|Htmlable|null
    {
        return $this->evaluateWithRecord($this->getOptionIconFromRecordUsing, $record);
    }

    public function getOptionColorFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionColorFromRecordUsing = $callback;
        $this->cachedColors = null;
        $this->hasCachedColors = false;

        return $this;
    }

    public function hasOptionColorFromRecordUsingCallback(): bool
    {
        return $this->hasRelationship() && ($this->getOptionColorFromRecordUsing !== null);
    }

    /**
     * @return string | array<int | string, string | int> | null
     */
    public function getOptionColorFromRecord(Model $record): string|array|null
    {
        return $this->evaluateWithRecord($this->getOptionColorFromRecordUsing, $record);
    }

    /**
     * Map every relationship record to a value using one of the `getOption*FromRecordUsing()` callbacks.
     *
     * @return array<array-key, mixed>
     */
    protected function mapRelationshipRecords(Closure $callback): array
    {
        $keyName = $this->getRelationshipRelatedKeyName();

        return $this->getRelationshipRecords()
            ->mapWithKeys(static fn (Model $record): array => [
                $record->getAttribute($keyName) => $callback($record),
            ])
            ->filter(static fn (mixed $value): bool => filled($value))
            ->all();
    }

    protected function evaluateWithRecord(?Closure $callback, Model $record): mixed
    {
        return $this->evaluate(
            $callback,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ],
        );
    }
}
