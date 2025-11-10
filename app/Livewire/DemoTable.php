<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Livewire\Component;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class DemoTable extends Component implements HasTable,HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('username')
                    ->label('Username')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->getStateUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                    ->sortable()
                    ->searchable(),
            ]);
    }

    public function render()
    {
        return view('livewire.demo-table');
    }
}
