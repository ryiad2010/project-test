<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $width = $getWidth();
    @endphp
    <div x-load
        x-load-src={{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('color-picker', 'ryiad/filament-toolkit') }}
        x-data="colorPicker({ state: $wire.$entangle('{{ $getStatePath() }}'), width: @js($width) })">

        <div wire:ignore x-ref="picker"></div>
    </div>
</x-dynamic-component>
