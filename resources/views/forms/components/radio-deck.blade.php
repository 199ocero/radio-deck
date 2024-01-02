@php
    $id = $getId();
    $isDisabled = $isDisabled();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament::grid :default="$getColumns('default')" :sm="$getColumns('sm')" :md="$getColumns('md')" :lg="$getColumns('lg')" :xl="$getColumns('xl')"
        :two-xl="$getColumns('2xl')" is-grid @class(['gap-5'])>
        @foreach ($getOptions() as $value => $label)
            @php
                $shouldOptionBeDisabled = $isDisabled || $isOptionDisabled($value, $label);
            @endphp

            <x-filament::input.wrapper>
                <label class="flex cursor-pointer gap-x-3">
                    <input @disabled($shouldOptionBeDisabled) id="{{ $id }}-{{ $value }}"
                        name="{{ $id }}" type="radio" value="{{ $value }}" wire:loading.attr="disabled"
                        {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
                        {{ $getExtraInputAttributeBag()->class(['peer hidden']) }} />

                    @php
                        $iconExists = $hasIcons($value);
                        $iconPosition = $getIconPosition();
                        $alignment = $getAlignment();
                        $color = $getColor();
                        $icon = $getIcon($value);
                        $iconSize = $getIconSize();
                        $descriptionExists = $hasDescription($value);
                        $description = $getDescription($value);
                    @endphp
                    <div @class([
                        'flex px-4 py-2 w-full text-sm leading-6 rounded-lg gap-5',
                        $iconExists
                            ? match ($iconPosition) {
                                \Filament\Support\Enums\IconPosition::Before,
                                'before'
                                    => 'justify-start',
                                \Filament\Support\Enums\IconPosition::After,
                                'after'
                                    => 'justify-between flex-row-reverse',
                                default => 'justify-start',
                            }
                            : 'justify-start',
                        match ($alignment) {
                            \Filament\Support\Enums\Alignment::Center, 'center' => 'items-center',
                            \Filament\Support\Enums\Alignment::Start, 'start' => 'items-start',
                            \Filament\Support\Enums\Alignment::End, 'end' => 'items-end',
                            default => 'items-center',
                        },
                        'ring-1 ring-gray-200 dark:ring-gray-700 peer-checked:ring-2',
                        'peer-disabled:bg-gray-100/50 dark:peer-disabled:bg-gray-700/50 peer-disabled:cursor-not-allowed',
                        match ($color) {
                            'gray' => 'peer-checked:ring-gray-600 dark:peer-checked:ring-gray-700',
                            default
                                => 'fi-color-custom ring-custom-600 dark:peer-checked:ring-custom-700',
                        },
                    ]) @style([
                        \Filament\Support\get_color_css_variables($color, shades: [600, 700]) => $color !== 'gray',
                    ])>
                        @if ($iconExists)
                            <x-filament::icon :icon="$icon" @class([
                                'flex-shrink-0',
                                match ($iconSize) {
                                    \Filament\Support\Enums\IconSize::Small => 'h-8 w-8',
                                    'sm' => 'h-8 w-8',
                                    \Filament\Support\Enums\IconSize::Medium => 'h-9 w-9',
                                    'md' => 'h-9 w-9',
                                    \Filament\Support\Enums\IconSize::Large => 'h-10 w-10',
                                    'lg' => 'h-10 w-10',
                                    default => 'h-8 w-8',
                                },
                                match ($color) {
                                    'gray' => 'fi-color-gray text-gray-600 dark:text-gray-500',
                                    default => 'fi-color-custom text-custom-600 dark:text-custom-500',
                                },
                            ]) @style([
                                \Filament\Support\get_color_css_variables($color, shades: [600, 500]) => $color !== 'gray',
                            ]) />
                        @endif
                        <div class="place-items-start">
                            <span class="font-medium text-gray-950 dark:text-white">
                                {{ $label }}
                            </span>

                            @if ($descriptionExists)
                                <p class="text-gray-500 dark:text-gray-400">
                                    {{ $description }}
                                </p>
                            @endif
                        </div>
                    </div>
                </label>
            </x-filament::input.wrapper>
        @endforeach
    </x-filament::grid>
</x-dynamic-component>
