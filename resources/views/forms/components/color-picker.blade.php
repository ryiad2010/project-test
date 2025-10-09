<script src="https://cdn.jsdelivr.net/npm/@jaames/iro@5"></script>
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $width = $getWidth();
    @endphp
    <div
        x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }"
        x-init="
            $nextTick(() => {
                const picker = new iro.ColorPicker($refs.picker, {
                    @if ($width) width: @js($width), @endif
                    color: state || '#ffffff',
                });
                picker.on('color:change', (color) => {
                    state = color.hexString;
                });
            })
        "
        class="w-full"
    >
        <div wire:ignore x-ref="picker" class="flex justify-center items-center"></div>
    </div>
</x-dynamic-component>
