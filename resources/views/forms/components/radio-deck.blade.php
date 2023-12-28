@php
    $gridDirection = $getGridDirection() ?? 'column';
    $id = $getId();
    $isDisabled = $isDisabled();
    $isInline = $isInline();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <x-filament::grid
        :default="$getColumns('default')"
        :sm="$getColumns('sm')"
        :md="$getColumns('md')"
        :lg="$getColumns('lg')"
        :xl="$getColumns('xl')"
        :two-xl="$getColumns('2xl')"
        :is-grid="! $isInline"
        :direction="$gridDirection"
        :attributes="
            \Filament\Support\prepare_inherited_attributes($attributes)
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-fo-radio gap-4',
                    '-mt-4' => (! $isInline) && $gridDirection === 'column',
                    'flex flex-wrap' => $isInline,
                ])
        "
    >
        @foreach ($getOptions() as $value => $label)
            @php
                $shouldOptionBeDisabled = $isDisabled || $isOptionDisabled($value, $label);
            @endphp

            <div
                @class([
                    'break-inside-avoid pt-4' => (! $isInline) && $gridDirection === 'column',
                ])
            >
            <x-filament::input.wrapper>
                <label class="flex cursor-pointer gap-x-3">
                    <input
                        @disabled($shouldOptionBeDisabled)
                        id="{{ $id }}-{{ $value }}"
                        name="{{ $id }}"
                        type="radio"
                        value="{{ $value }}"
                        wire:loading.attr="disabled"
                        {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
                        {{ $getExtraInputAttributeBag()->class(['peer hidden']) }}
                    />

                    <div @class([
                        "flex px-4 py-2 w-full text-sm leading-6 rounded-lg gap-5",
                        ($hasIcons($value))
                            ? match($getIconPosition()) {
                                \Filament\Support\Enums\IconPosition::Before => 'justify-start',
                                'before' => 'justify-start',
                                \Filament\Support\Enums\IconPosition::After => 'justify-between flex-row-reverse',
                                'after' => 'justify-between flex-row-reverse',
                                default => 'justify-start',
                            }
                            : 'justify-start',
                        match ($getAlignment()) {
                            \Filament\Support\Enums\Alignment::Center => 'items-center',
                            'center' => 'items-center',
                            \Filament\Support\Enums\Alignment::Start => 'items-start',
                            'start' => 'items-start',
                            \Filament\Support\Enums\Alignment::End => 'items-end',
                            'end' => 'items-end',
                            default => 'items-center'
                        },
                        "ring-1 ring-gray-200 dark:ring-gray-700 peer-checked:ring-2",
                        match ($getColor()) {
                            'danger' => 'peer-checked:ring-danger-700',
                            'gray' => 'peer-checked:ring-gray-700',
                            'info' => 'peer-checked:ring-info-700',
                            'primary' => 'peer-checked:ring-primary-700',
                            'success' => 'peer-checked:ring-success-700',
                            'warning' => 'peer-checked:ring-warning-700',
                            default => 'peer-checked:ring-primary-700',
                        },
                        "peer-disabled:bg-gray-100/50 dark:peer-disabled:bg-gray-700/50 peer-disabled:cursor-not-allowed",
                    ])>
                        @if ($hasIcons($value))
                            <x-filament::icon
                                :icon="$getIcon($value)"
                                @class([
                                    'flex-shrink-0',
                                    match ($getIconSize()) {
                                        \Filament\Support\Enums\IconSize::Small => 'h-8 w-8',
                                        'sm' => 'h-8 w-8',
                                        \Filament\Support\Enums\IconSize::Medium => 'h-9 w-9',
                                        'md' => 'h-9 w-9',
                                        \Filament\Support\Enums\IconSize::Large => 'h-10 w-10',
                                        'lg' => 'h-10 w-10',
                                        default => 'h-8 w-8',
                                    },
                                    match ($getColor()) {
                                        'danger' => 'text-danger-400 dark:text-danger-500',
                                        'gray' => 'text-gray-400 dark:text-gray-500',
                                        'info' => 'text-info-400 dark:text-info-500',
                                        'primary' => 'text-primary-400 dark:text-primary-500',
                                        'success' => 'text-success-400 dark:text-success-500',
                                        'warning' => 'text-warning-400 dark:text-warning-500',
                                        default => 'text-gray-400 dark:text-gray-500',
                                    }
                                ])
                            />
                        @endif
                        <div class="place-items-start">
                            <span class="font-medium text-gray-950 dark:text-white">
                                {{ $label }}
                            </span>
    
                            @if ($hasDescription($value))
                                <p class="text-gray-500 dark:text-gray-400">
                                    {{ $getDescription($value) }}
                                </p>
                            @endif
                        </div>
                    </div>
                </label>
            </x-filament::input.wrapper>
                
            </div>
        @endforeach
    </x-filament::grid>
</x-dynamic-component>