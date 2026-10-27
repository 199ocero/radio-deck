<?php

use Filament\Schemas\Components\Component;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\GridDirection;
use Filament\Support\Icons\Heroicon;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;
use JaOcero\RadioDeck\Tests\Fixtures\Enums\LegacyPlatform;
use JaOcero\RadioDeck\Tests\Fixtures\Enums\Platform;
use JaOcero\RadioDeck\Tests\Fixtures\Livewire\TestForm;
use JaOcero\RadioDeck\Tests\Fixtures\Models\Category;
use JaOcero\RadioDeck\Tests\Fixtures\Models\Post;
use JaOcero\RadioDeck\Tests\Fixtures\Models\Tag;
use Livewire\Features\SupportTesting\Testable;

use function Pest\Livewire\livewire;

/**
 * @param  array<Component> | Component  $components
 */
function radioDeckForm(array|Component $components, ?Post $record = null): Testable
{
    TestForm::$components = fn (): array => is_array($components) ? $components : [$components];

    return livewire(TestForm::class, ['record' => $record]);
}

function getRadioDeck(Testable $livewire, string $name): RadioDeck
{
    return collect($livewire->instance()->form->getFlatFields(withHidden: true))
        ->first(fn ($field): bool => $field->getName() === $name);
}

describe('enum resolution', function () {
    it('resolves labels, descriptions, icons and colors from Filament enum contracts', function () {
        $livewire = radioDeckForm(RadioDeck::make('platform')->options(Platform::class));
        $field = getRadioDeck($livewire, 'platform');

        expect($field->getOptions())->toBe(['ios' => 'iOS', 'android' => 'Android', 'web' => 'Web'])
            ->and($field->getDescription('web'))->toBe('Web app')
            ->and($field->getIcon('web'))->toBe(Heroicon::GlobeAlt)
            ->and($field->getOptionColor('android'))->toBe('success');

        $livewire
            ->assertSeeHtml('role="radiogroup"')
            ->assertSee('iOS mobile app');
    });

    it('keeps supporting the deprecated HasIcons and HasDescriptions contracts', function () {
        $livewire = radioDeckForm(
            RadioDeck::make('platform')
                ->options(LegacyPlatform::class)
                ->descriptions(LegacyPlatform::class)
                ->icons(LegacyPlatform::class),
        );
        $field = getRadioDeck($livewire, 'platform');

        expect($field->getDescription('linux'))->toBe('Linux desktop app')
            ->and($field->getIcon('linux'))->toBe('heroicon-o-computer-desktop')
            ->and($field->hasIcons('linux'))->toBeTrue();

        $livewire->assertSee('Windows desktop app');
    });

    it('lets explicit arrays and closures override the enum', function () {
        $field = getRadioDeck(radioDeckForm(
            RadioDeck::make('platform')
                ->options(Platform::class)
                ->descriptions(fn (): array => ['ios' => 'Custom'])
                ->icons(['ios' => Heroicon::Star])
                ->colors(['ios' => Color::Rose]),
        ), 'platform');

        expect($field->getDescriptions())->toBe(['ios' => 'Custom'])
            ->and($field->getIcon('ios'))->toBe(Heroicon::Star)
            ->and($field->getIcon('web'))->toBeNull()
            ->and($field->getOptionColor('ios'))->toBe(Color::Rose)
            ->and($field->getOptionColor('web'))->toBe('primary');
    });

    it('applies a single color to every option', function () {
        $field = getRadioDeck(radioDeckForm(
            RadioDeck::make('platform')->options(['a' => 'A', 'b' => 'B'])->colors('danger'),
        ), 'platform');

        expect($field->getOptionColor('a'))->toBe('danger')
            ->and($field->getOptionColor('b'))->toBe('danger');
    });
});

describe('state and validation', function () {
    it('casts the selected value to the model enum', function () {
        radioDeckForm(RadioDeck::make('platform')->options(Platform::class)->required())
            ->set('data.platform', 'android')
            ->call('save')
            ->assertHasNoErrors();

        expect(Post::sole()->platform)->toBe(Platform::Android);
    });

    it('rejects values that are not one of the options', function () {
        radioDeckForm(RadioDeck::make('platform')->options(Platform::class))
            ->set('data.platform', 'blackberry')
            ->call('save')
            ->assertHasErrors(['data.platform']);

        expect(Post::count())->toBe(0);
    });

    it('rejects disabled options', function () {
        radioDeckForm(
            RadioDeck::make('platform')
                ->options(Platform::class)
                ->disableOptionWhen(fn (string $value): bool => $value === 'web'),
        )
            ->set('data.platform', 'web')
            ->call('save')
            ->assertHasErrors(['data.platform']);
    });

    it('stores multiple selections as an array and validates each value', function () {
        $components = fn () => RadioDeck::make('targets')
            ->options(['eu' => 'Europe', 'us' => 'United States', 'ap' => 'Asia Pacific'])
            ->multiple();

        radioDeckForm($components())
            ->set('data.targets', ['eu', 'nope'])
            ->call('save')
            ->assertHasErrors(['data.targets.1']);

        radioDeckForm($components())
            ->set('data.targets', ['eu', 'ap'])
            ->call('save')
            ->assertHasNoErrors();

        expect(Post::sole()->targets)->toBe(['eu', 'ap']);
    });

    it('enforces min and max items', function () {
        $components = fn () => RadioDeck::make('targets')
            ->options(['eu' => 'Europe', 'us' => 'United States', 'ap' => 'Asia Pacific'])
            ->multiple()
            ->minItems(2)
            ->maxItems(2);

        radioDeckForm($components())
            ->set('data.targets', ['eu'])
            ->call('save')
            ->assertHasErrors(['data.targets' => 'min']);

        radioDeckForm($components())
            ->set('data.targets', ['eu', 'us', 'ap'])
            ->call('save')
            ->assertHasErrors(['data.targets' => 'max']);
    });

    it('supports boolean options', function () {
        $livewire = radioDeckForm(RadioDeck::make('is_published')->boolean('Published', 'Draft'));

        expect(getRadioDeck($livewire, 'is_published')->getOptions())->toBe([1 => 'Published', 0 => 'Draft']);

        $livewire
            ->set('data.is_published', '1')
            ->call('save')
            ->assertHasNoErrors();

        expect(Post::sole()->is_published)->toBeTrue();
    });
});

describe('relationships', function () {
    it('loads BelongsTo options from the related records and saves the foreign key', function () {
        $design = Category::create(['name' => 'Design', 'description' => 'Pixels', 'icon' => 'heroicon-o-paint-brush', 'color' => 'info']);
        Category::create(['name' => 'Code', 'description' => 'Bits', 'icon' => 'heroicon-o-code-bracket', 'color' => 'success']);

        $livewire = radioDeckForm(
            RadioDeck::make('category_id')
                ->relationship('category', 'name')
                ->getOptionDescriptionFromRecordUsing(fn (Category $record): string => $record->description)
                ->getOptionIconFromRecordUsing(fn (Category $record): string => $record->icon)
                ->getOptionColorFromRecordUsing(fn (Category $record): string => $record->color),
        );
        $field = getRadioDeck($livewire, 'category_id');

        expect($field->getOptions())->toBe([2 => 'Code', 1 => 'Design'])
            ->and($field->getDescription(1))->toBe('Pixels')
            ->and($field->getIcon(1))->toBe('heroicon-o-paint-brush')
            ->and($field->getOptionColor(1))->toBe('info')
            ->and($field->getLabel())->toBe('Category');

        $livewire
            ->set('data.category_id', $design->getKey())
            ->call('save')
            ->assertHasNoErrors();

        expect(Post::sole()->category_id)->toBe($design->getKey());
    });

    it('syncs BelongsToMany selections with pivot data and loads them back', function () {
        [$laravel, $filament, $livewireTag] = collect(['Laravel', 'Filament', 'Livewire'])
            ->map(fn (string $name): Tag => Tag::create(['name' => $name]))
            ->all();

        $component = fn () => RadioDeck::make('tags')
            ->relationship(titleAttribute: 'name')
            ->pivotData(['note' => 'picked'])
            ->multiple();

        radioDeckForm($component())
            ->set('data.tags', [(string) $laravel->getKey(), (string) $filament->getKey()])
            ->call('save')
            ->assertHasNoErrors();

        $post = Post::sole();

        expect($post->tags->pluck('name')->sort()->values()->all())->toBe(['Filament', 'Laravel'])
            ->and($post->tags->first()->pivot->note)->toBe('picked');

        radioDeckForm($component(), $post)
            ->assertSet('data.tags', fn (array $state): bool => collect($state)->map(strval(...))->sort()->values()->all() === [(string) $laravel->getKey(), (string) $filament->getKey()])
            ->set('data.tags', [(string) $livewireTag->getKey()])
            ->call('save')
            ->assertHasNoErrors();

        expect($post->refresh()->tags->pluck('name')->all())->toBe(['Livewire']);
    });
});

describe('rendering', function () {
    it('renders the search input, bulk toggle actions and Alpine component', function () {
        radioDeckForm(
            RadioDeck::make('targets')
                ->options(['eu' => 'Europe', 'us' => 'United States'])
                ->multiple()
                ->searchable()
                ->bulkToggleable(),
        )
            ->assertSeeHtml('x-data="radioDeckFormComponent()"')
            ->assertSeeHtml('role="group"')
            ->assertSeeHtml('type="search"')
            ->assertSee('Select all');
    });

    it('does not render bulk toggle actions for single selection', function () {
        radioDeckForm(
            RadioDeck::make('platform')->options(Platform::class)->bulkToggleable(),
        )
            ->assertDontSee('Select all')
            ->assertDontSeeHtml('radioDeckFormComponent');
    });

    it('renders tooltips and the selection indicator', function () {
        radioDeckForm(
            RadioDeck::make('platform')
                ->options(Platform::class)
                ->tooltips(['ios' => 'Requires an Apple developer account'])
                ->selectionIndicator(),
        )
            ->assertSeeHtml('x-tooltip')
            ->assertSee('Requires an Apple developer account')
            ->assertSeeHtml('fi-fo-radio-deck-indicator');
    });

    it('lays out cards in a row-first grid by default', function () {
        radioDeckForm(RadioDeck::make('platform')->options(Platform::class)->columns(3))
            ->assertDontSeeHtml('fi-grid-direction-col')
            ->assertDontSeeHtml('break-inside-avoid');
    });

    it('spaces cards vertically when filling columns first', function () {
        radioDeckForm(
            RadioDeck::make('platform')
                ->options(Platform::class)
                ->columns(3)
                ->gridDirection(GridDirection::Column),
        )
            ->assertSeeHtml('fi-grid-direction-col')
            ->assertSeeHtml('break-inside-avoid pt-5');
    });

    it('reserves space for the selection indicator only with the default padding', function () {
        radioDeckForm(RadioDeck::make('platform')->options(Platform::class)->selectionIndicator())
            ->assertSeeHtml('py-2 ps-4 pe-10');

        radioDeckForm(RadioDeck::make('platform')->options(Platform::class)->selectionIndicator()->padding('px-4 py-6'))
            ->assertSeeHtml('px-4 py-6')
            ->assertDontSeeHtml('pe-10');
    });

    it('lets extra card classes control the main axis alignment', function () {
        radioDeckForm(
            RadioDeck::make('platform')
                ->options(Platform::class)
                ->extraCardsAttributes(['class' => 'flex-col justify-center']),
        )
            ->assertSeeHtml('justify-center')
            ->assertDontSeeHtml('justify-start');
    });

    it('aligns the selection indicator with the card content', function () {
        radioDeckForm(RadioDeck::make('platform')->options(Platform::class)->selectionIndicator())
            ->assertSeeHtml('top-1/2 -translate-y-1/2');

        radioDeckForm(RadioDeck::make('platform')->options(Platform::class)->selectionIndicator()->alignment(Alignment::Start))
            ->assertSeeHtml('end-3 flex size-5')
            ->assertDontSeeHtml('top-1/2');
    });

    it('marks inputs as accessible radios instead of hiding them', function () {
        radioDeckForm(RadioDeck::make('platform')->options(Platform::class)->required())
            ->assertSeeHtml('sr-only')
            ->assertDontSeeHtml('peer hidden')
            ->assertSeeHtml('aria-required="true"');
    });
});
