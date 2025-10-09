<?php

namespace Ryiad\FilamentToolkit\Tables\Filters;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class DateRangeFilter extends Filter
{
    protected string | \DateTimeInterface | \Closure | null $maxDate = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->form(fn() => [
            Fieldset::make($this->getLabel())->schema([
                DatePicker::make('from')
                    ->native(false)
                    ->maxDate($this->getMaxDate())
                    // Format the default date into a string for the DatePicker.
                    ->default(
                        optional(Arr::get($this->getDefaultState(), 'from'))->format('Y-m-d')
                    ),

                DatePicker::make('to')
                    ->native(false)
                    ->maxDate($this->getMaxDate())
                    // Format the default date into a string for the DatePicker.
                    ->default(
                        optional(Arr::get($this->getDefaultState(), 'to'))->format('Y-m-d')
                    ),
            ])->columns(1),
        ])
            ->query(function (Builder $query, array $data): Builder {
                // Use Carbon to parse the date and ensure we only use the date part for the query.
                // This handles both 'Y-m-d' strings from the picker and 'Y-m-d H:i:s' from the default state.
                return $query
                    ->when(
                        $data['from'] ?? null,
                        fn(Builder $query, $date): Builder => $query->whereDate($this->getName(), '>=', Carbon::parse($date))
                    )
                    ->when(
                        $data['to'] ?? null,
                        fn(Builder $query, $date): Builder => $query->whereDate($this->getName(), '<=', Carbon::parse($date))
                    );
            });
    }

    /**
     * Set the default state for the filter.
     * The method signature is updated to be compatible with the parent class.
     *
     * @param mixed $state
     */
    public function default(mixed $state = true): static
    {
        // This flag tells Filament to apply the filter by default on page load.

        // We only set the default state if an array is passed.
        if (is_array($state)) {
            $this->defaultState = $state;
        }

        return $this;
    }

    public function maxDate(string | \DateTimeInterface | \Closure | null $date): static
    {
        $this->maxDate = $date;
        return $this;
    }

    public function getMaxDate(): string | \DateTimeInterface | null
    {
        return $this->evaluate($this->maxDate);
    }
}
