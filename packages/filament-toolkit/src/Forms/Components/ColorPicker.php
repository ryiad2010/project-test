<?php

namespace Ryiad\FilamentToolkit\Forms\Components;

use Filament\Forms\Components\Field;

class ColorPicker extends Field
{
    protected string $view = 'filament-toolkit::forms.components.color-picker';
    protected int | \Closure | null $width = null;

    public function width(int $width): static
    {
        $this->width = $width;
        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->evaluate($this->width);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure the component respects grid layout
        $this->columnSpan([
            'default' => 1,
            'sm' => 1,
            'md' => 1,
            'lg' => 1,
            'xl' => 1,
            '2xl' => 1,
        ]);
    }
}
