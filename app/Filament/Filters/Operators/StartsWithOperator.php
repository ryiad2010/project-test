<?php

namespace App\Filament\Filters\Operators;

use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;

class StartsWithOperator extends Operator
{




    public function getName(): string
    {
        return 'app_starts_with_operator';
    }


    public function getLabel(): string
    {
        return 'Start With string [' . spl_object_hash($this) . ']';
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $settings = $this->getSettings() ?? [];
        $value = (string) $this->evaluate($settings['value'] ?? '');

        if ($value === '') {
            return $query;
        }

        return $query->where($qualifiedColumn, 'LIKE', $value . '%');
    }

    public function getFormSchema(): array
    {
        return [
            \Filament\Forms\Components\TextInput::make('value')
                ->label('Value')
                ->required(),
        ];
    }

    // ← important: ALWAYS return a string
    public function getSummary(): string
    {
        $settings = $this->getSettings() ?? [];
        $value = $this->evaluate($settings['value'] ?? '');


        // ensure a string is returned (even if empty)
        return $value === null ? '' : (string) $value;
    }
}
