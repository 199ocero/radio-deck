@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\GridDirection;
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Enums\IconSize;
    use Filament\Support\Facades\FilamentAsset;
    use Filament\Support\Icons\Heroicon;
    use Illuminate\Contracts\Support\Htmlable;
    use Illuminate\View\ComponentAttributeBag;

    use function Filament\Support\generate_icon_html;
    use function Filament\Support\get_color_css_variables;

    $id = $getId();
    $isDisabled = $isDisabled();
    $isMultiple = $isMultiple();
    $isInline = $isInline();
    $isRequired = $isRequired();
    $isSearchable = $isSearchable();
    $isBulkToggleable = $isBulkToggleable() && (! $isDisabled);
    $hasSelectionIndicator = $hasSelectionIndicator();
    $hasAlpineComponent = $isSearchable || $isBulkToggleable;
    $statePath = $getStatePath();
    $options = $getOptions();
    $livewireKey = $getLivewireKey();
    $wireModelAttribute = $applyStateBindingModifiers('wire:model');
    $hasError = $errors->has($statePath) || $errors->has("{$statePath}.*");
    $accessibilityAttributes = method_exists($field, 'getAccessibilityAttributes') ? $field->getAccessibilityAttributes() : [];

    $iconPosition = $getIconPosition();
    $alignment = $getAlignment();
    $gap = $getOptionsGap();
    $padding = $getPadding();
    $iconSizeValue = $getIconSize() ?? 'md';
    $iconSize = IconSize::tryFrom($iconSizeValue) ?? IconSize::Medium;
    $iconSizeClass = match ($iconSizeValue) {
        'xs' => 'size-4',
        'sm' => 'size-5',
        'md' => 'size-6',
        'lg' => 'size-8',
        'xl' => 'size-10',
        '2xl' => 'size-12',
        default => $iconSizeValue,
    };

    $gridDirection = $getGridDirection() ?? GridDirection::Row;
    $isColumnDirection = (! $isInline) && ($gridDirection === GridDirection::Column);

    $optionsAttributes = $getExtraAttributeBag();

    if (! $isInline) {
        $optionsAttributes = $optionsAttributes->grid($getColumns(), $gridDirection);
    }

    $optionsAttributes = $optionsAttributes
        ->merge([
            'aria-describedby' => $accessibilityAttributes['aria-describedby'] ?? null,
            'aria-invalid' => $hasError ? 'true' : null,
            'aria-labelledby' => filled($getLabel()) ? "{$id}-label" : null,
            'aria-required' => ((! $isMultiple) && $isRequired && (! $isDisabled)) ? 'true' : null,
            'role' => $isMultiple ? 'group' : 'radiogroup',
            'wire:key' => "{$livewireKey}.options",
            'x-show' => $isSearchable ? 'visibleOptionsCount !== 0' : null,
        ], escape: false)
        ->class([
            'fi-fo-radio-deck-options',
            'flex flex-wrap' => $isInline,
            'auto-cols-fr' => ! $isInline,
            // CSS columns only apply `gap` horizontally, so vertical spacing comes from padding on each option.
            '-mt-5' => $isColumnDirection,
            'gap-5',
        ]);
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field" label-tag="div">
    <div
        @if ($hasAlpineComponent)
            x-load
            x-load-src="{{ FilamentAsset::getAlpineComponentSrc('radio-deck', 'jaocero/radio-deck') }}"
            x-data="radioDeckFormComponent()"
            x-on:change="refresh()"
        @endif
        @class([
            'fi-fo-radio-deck flex flex-col gap-3',
            'fi-inline' => $isInline,
            'fi-multiple' => $isMultiple,
        ])
    >
        @if ($isSearchable)
            <div class="fi-input-wrp fi-fo-radio-deck-search-input-wrp">
                <div class="fi-input-wrp-prefix fi-input-wrp-prefix-has-content fi-inline">
                    {{ generate_icon_html(Heroicon::MagnifyingGlass, attributes: (new ComponentAttributeBag)->class(['fi-input-wrp-icon text-gray-400 dark:text-gray-500'])) }}
                </div>

                <div class="fi-input-wrp-content-ctn">
                    <input
                        aria-label="{{ $getSearchPrompt() }}"
                        placeholder="{{ $getSearchPrompt() }}"
                        type="search"
                        x-model.debounce.{{ $getSearchDebounce() }}="search"
                        class="fi-input fi-input-has-inline-prefix"
                    />
                </div>
            </div>
        @endif

        @if ($isBulkToggleable && count($options))
            <div
                x-cloak
                class="fi-fo-radio-deck-actions flex gap-x-3"
                wire:key="{{ $livewireKey }}.actions"
            >
                <span
                    x-show="! areAllCheckboxesChecked"
                    x-on:click="toggleAllCheckboxes()"
                    wire:key="{{ $livewireKey }}.actions.select-all"
                >
                    {{ $getAction('selectAll') }}
                </span>

                <span
                    x-show="areAllCheckboxesChecked"
                    x-on:click="toggleAllCheckboxes()"
                    wire:key="{{ $livewireKey }}.actions.deselect-all"
                >
                    {{ $getAction('deselectAll') }}
                </span>
            </div>
        @endif

        <div {{ $optionsAttributes }}>
            @foreach ($options as $value => $label)
                @php
                    $shouldOptionBeDisabled = $isDisabled || $isOptionDisabled($value, $label);
                    $color = $getOptionColor($value);
                    $icon = $getIcon($value);
                    $tooltip = $getTooltip($value);
                    $description = $getDescription($value);
                @endphp

                <div
                    wire:key="{{ $livewireKey }}.options.{{ $value }}"
                    @if ($isSearchable)
                        x-show="matchesSearch($el)"
                    @endif
                    @if (filled($tooltip))
                        x-tooltip="{
                            content: @js($tooltip),
                            theme: $store.theme,
                            allowHTML: @js($tooltip instanceof Htmlable),
                        }"
                    @endif
                    @class([
                        'fi-fo-radio-deck-option',
                        'break-inside-avoid pt-5' => $isColumnDirection,
                        'fi-disabled' => $shouldOptionBeDisabled,
                    ])
                    style="{{ get_color_css_variables($color, shades: [50, 400, 500, 600]) }}"
                >
                    <label class="group/radio-deck relative flex h-full cursor-pointer has-disabled:cursor-not-allowed">
                        <input
                            {{
                                $getExtraInputAttributeBag()
                                    ->merge([
                                        'aria-describedby' => $accessibilityAttributes['aria-describedby'] ?? null,
                                        'autofocus' => $loop->first && $isAutofocused(),
                                        'disabled' => $shouldOptionBeDisabled,
                                        'id' => "{$id}-{$value}",
                                        'name' => $isMultiple ? null : $id,
                                        'required' => (! $isMultiple) && $isRequired && (! $shouldOptionBeDisabled),
                                        'type' => $isMultiple ? 'checkbox' : 'radio',
                                        'value' => $value,
                                        'wire:loading.attr' => 'disabled',
                                        $wireModelAttribute => $statePath,
                                    ], escape: true)
                                    ->class(['fi-fo-radio-deck-input peer sr-only'])
                            }}
                        />

                        <div
                            {{
                                $getExtraCardsAttributeBag()->class([
                                    'fi-fo-radio-deck-card group/radio-deck-card relative flex w-full rounded-lg bg-white text-sm leading-6 shadow-xs ring-1 transition duration-75 dark:bg-white/5',
                                    $padding ?: ($hasSelectionIndicator ? 'py-2 ps-4 pe-10' : 'px-4 py-2'),
                                    $gap ?: 'gap-5',
                                    match ($iconPosition) {
                                        IconPosition::After, 'after' => 'flex-row-reverse justify-between',
                                        default => null,
                                    },
                                    match ($alignment) {
                                        Alignment::Start, 'start' => 'items-start',
                                        Alignment::End, 'end' => 'items-end',
                                        default => 'items-center',
                                    },
                                    $hasError ? 'ring-danger-600 dark:ring-danger-500' : 'ring-gray-950/10 dark:ring-white/20',
                                    'hover:bg-gray-50 dark:hover:bg-white/10',
                                    'peer-checked:bg-custom-50 peer-checked:ring-2 peer-checked:ring-custom-600 dark:peer-checked:bg-custom-500/10 dark:peer-checked:ring-custom-500',
                                    'peer-focus-visible:ring-2 peer-focus-visible:ring-custom-600 dark:peer-focus-visible:ring-custom-500',
                                    'peer-disabled:bg-gray-50 peer-disabled:opacity-70 dark:peer-disabled:bg-transparent',
                                ])
                            }}
                        >
                            @if (filled($icon))
                                {{
                                    generate_icon_html($icon, size: $iconSize, attributes: (new ComponentAttributeBag)->class([
                                        'fi-fo-radio-deck-icon shrink-0 text-custom-600 dark:text-custom-400',
                                        $iconSizeClass,
                                    ]))
                                }}
                            @endif

                            <div {{ $getExtraOptionsAttributeBag()->class(['fi-fo-radio-deck-text grid']) }}>
                                <span class="fi-fo-radio-deck-label font-medium text-gray-950 dark:text-white">
                                    {{ $label }}
                                </span>

                                @if (filled($description))
                                    <p {{ $getExtraDescriptionsAttributeBag()->class(['fi-fo-radio-deck-description text-gray-500 dark:text-gray-400']) }}>
                                        {{ $description }}
                                    </p>
                                @endif
                            </div>

                            @if ($hasSelectionIndicator)
                                <span
                                    aria-hidden="true"
                                    @class([
                                        'fi-fo-radio-deck-indicator absolute end-3 flex size-5 items-center justify-center border border-gray-300 bg-white text-white dark:border-white/20 dark:bg-white/5',
                                        match ($alignment) {
                                            Alignment::Start, 'start' => 'top-3',
                                            Alignment::End, 'end' => 'bottom-3',
                                            default => 'top-1/2 -translate-y-1/2',
                                        },
                                        // Stacked cards (`flex-col`) keep the indicator in the top corner.
                                        'group-[.flex-col]/radio-deck-card:top-3 group-[.flex-col]/radio-deck-card:bottom-auto group-[.flex-col]/radio-deck-card:translate-y-0',
                                        'group-has-checked/radio-deck:border-transparent group-has-checked/radio-deck:bg-custom-600 dark:group-has-checked/radio-deck:bg-custom-500',
                                        $isMultiple ? 'rounded-md' : 'rounded-full',
                                    ])
                                >
                                    <span class="hidden group-has-checked/radio-deck:inline-flex">
                                        {{ generate_icon_html(Heroicon::Check, size: IconSize::Small, attributes: (new ComponentAttributeBag)->class(['size-3.5'])) }}
                                    </span>
                                </span>
                            @endif
                        </div>
                    </label>
                </div>
            @endforeach
        </div>

        @if ($isSearchable)
            <div
                x-cloak
                x-show="search && visibleOptionsCount === 0"
                role="status"
                aria-live="polite"
                class="fi-fo-radio-deck-no-search-results-message text-sm text-gray-500 dark:text-gray-400"
            >
                {{ $getNoSearchResultsMessage() }}
            </div>
        @endif
    </div>
</x-dynamic-component>
