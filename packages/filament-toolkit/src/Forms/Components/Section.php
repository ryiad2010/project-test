<?php

namespace Ryiad\FilamentToolkit\Forms\Components;

use Filament\Forms\Components\Component;

class Section extends Component
{
    protected string $view = 'filament-toolkit::section';
    protected string | \Closure | null $description = null;
    public function __construct(
        protected string | \Closure $heading,
    ) {}

    public static function make(string | \Closure $heading): static
    {
        return app(static::class, [
            'heading' => $heading
        ]);
    }

    public function getHeading(): string | \Closure
    {
        return $this->evaluate($this->heading);
    }
    public function description(string | \Closure $description): static
    {
        $this->description = $description;
        return $this;
    }
    public function getDescription(): string | \Closure | null
    {
        return $this->evaluate($this->description);
    }
}
