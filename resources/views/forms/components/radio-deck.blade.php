@php
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\IconSize;

    $id = $getId();
    $isDisabled = $isDisabled();
    $isMultiple = $isMultiple();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div {{ $attributes->grid($getColumns())->class(['auto-cols-fr', 'gap-5']) }}>
        @foreach ($getOptions() as $value => $label)
            @php
                $shouldOptionBeDisabled = $isDisabled || $isOptionDisabled($value, $label);
            @endphp

            <label class="flex cursor-pointer gap-x-3">
                <input @disabled($shouldOptionBeDisabled) id="{{ $id }}-{{ $value }}"
                    @if (!$isMultiple) name="{{ $id }}" @endif
                    type="{{ $isMultiple ? 'checkbox' : 'radio' }}" value="{{ $value }}"
                    wire:loading.attr="disabled" {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
                    {{ $getExtraInputAttributeBag()->class(['peer hidden']) }} />

                @php
                    $iconExists = $hasIcons($value);
                    $iconPosition = $getIconPosition();
                    $alignment = $getAlignment();
                    $gap = $getGap();
                    $padding = $getPadding();

                    $color = $getOptionColor($value);

                    $icon = $getIcon($value);
                    $descriptionExists = $hasDescription($value);
                    $description = $getDescription($value);
                @endphp
                <div {{ $getExtraCardsAttributeBag()->class([
                    'flex w-full text-sm leading-6 rounded-lg bg-white dark:bg-gray-900',
                    $padding ?: 'px-4 py-2',
                    $gap ?: 'gap-5',
                    $iconExists
                        ? match ($iconPosition) {
                            'before' => 'justify-start',
                            'after' => 'justify-between flex-row-reverse',
                            default => 'justify-start',
                        }
                        : 'justify-start',
                    match ($alignment) {
                        Alignment::Center, 'center' => 'items-center',
                        Alignment::Start, 'start' => 'items-start',
                        Alignment::End, 'end' => 'items-end',
                        default => 'items-center',
                    },
                    'ring-1 ring-gray-200 dark:ring-gray-700 peer-checked:ring-2',
                    'peer-disabled:bg-gray-100/50 dark:peer-disabled:bg-gray-700/50 peer-disabled:cursor-not-allowed',
                    match ($color) {
                        'gray' => 'peer-checked:ring-gray-600 dark:peer-checked:ring-gray-500',
                        default => 'fi-color-custom peer-checked:ring-custom-600 dark:peer-checked:ring-custom-500',
                    },
                ]) }}
                    @style([
                        \Filament\Support\get_color_css_variables($color, shades: [600, 500]) => $color !== 'gray',
                    ])>
                    @if ($iconExists)
                        @php
                            $iconSizeValue = $getIconSizes('md');

                            if ($iconSizeValue instanceof \Filament\Support\Enums\IconSize) {
                                $iconSizeValue = $iconSizeValue->value;
                            }
                            $iconSizeClass = match ($iconSizeValue) {
                                'xs' => 'h-4 w-4',
                                'sm' => 'h-5 w-5',
                                'md' => 'h-6 w-6',
                                'lg' => 'h-8 w-8',
                                'xl' => 'h-10 w-10',
                                '2xl' => 'h-12 w-12',
                                null => 'h-6 w-6',
                                default => $iconSizeValue,
                            };
                        @endphp

                        <x-filament::icon :icon="$icon" @class([
                            'flex-shrink-0',
                            $iconSizeClass,
                            match ($color) {
                                'gray' => 'fi-color-gray text-gray-600 dark:text-gray-500',
                                default => 'fi-color-custom text-custom-600 dark:text-custom-500',
                            },
                        ]) @style([
                            \Filament\Support\get_color_css_variables($color, shades: [600, 500]) => $color !== 'gray',
                        ]) />
                    @endif
                    <div {{ $getExtraOptionsAttributeBag()->merge(['class' => 'place-items-start']) }}>
                        <span class="font-medium text-gray-950 dark:text-white">
                            {{ $label }}
                        </span>

                        @if ($descriptionExists)
                            <p
                                {{ $getExtraDescriptionsAttributeBag()->merge(['class' => 'text-gray-500 dark:text-gray-400']) }}>
                                {{ $description }}
                            </p>
                        @endif
                    </div>
                </div>
            </label>
        @endforeach
    </div>
</x-dynamic-component>
