<?php

namespace Ryiad\FilamentToolkit\Resources;

use Ryiad\FilamentToolkit\Resources\UserResource\Pages;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Filament\Filters\Operators\StartsWithOperator;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_action')
                    ->required(),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\Toggle::make('is_admin')
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->selectable()
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->rowIndex(),
                Panel::make([
                    Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                            ->weight(FontWeight::Bold)
                            ->fontFamily(FontFamily::Serif)
                            //   ->copyable()
                            //    ->copyMessage('Name has been copied')
                            // ->copyableState(fn(string $state): string => "URL: {$state}")
                            //  ->copyMessageDuration(1500)
                            ->searchable(isIndividual: true, isGlobal: false)
                            ->wrapHeader()
                            ->grow()
                            ->verticalAlignment(VerticalAlignment::End)
                            ->action(
                                Action::make('select')->requiresConfirmation()
                                    ->action(function ($record): void {
                                        Notification::make()
                                            ->title('Post selected successfully!' . $record->name)
                                            ->success()
                                            ->send();
                                    }),

                            ),
                        ColumnGroup::make('Email Information')->columns([

                            Tables\Columns\TextColumn::make('email')
                                ->searchable()
                                ->verticalAlignment(VerticalAlignment::End)
                                ->tooltip('Official Email')
                                ->prefix('https://')
                                ->suffix('.com')
                                ->width('10%')
                                ->extraAttributes(['class' => 'bg-gray-200'])
                                ->url(fn($record): string => route('filament.admin.resources.users.edit', $record))
                                ->openUrlInNewTab(),
                        ]),
                    ])->collapsible(),

                    Tables\Columns\IconColumn::make('is_action')
                        ->boolean(),
                    Tables\Columns\TextColumn::make('email_verified_at')
                        ->dateTime()
                        ->wrapHeader()
                        ->placeholder('No description.')
                        ->sortable()
                        ->visible(filament('ryiad-toolkit')->hasEmailVerifiedAt()),

                ])->alignment(Alignment::Center)
                    ->wrapHeader(),
                Tables\Columns\IconColumn::make('is_admin')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('status')
                    ->icon(fn(string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'reviewing' => 'heroicon-o-clock',
                        'approved' => 'heroicon-o-check-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'info',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    })
                    ->size(IconColumn\IconColumnSize::ExtraLarge)
            ])->searchOnBlur()
            ->defaultSort('created_at', 'desc')
            ->defaultSortOptionLabel('Recently created')
            ->searchPlaceholder('Search (ID, Name)')
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->toggleColumnsTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Toggle columns'),
            )

            ->persistSortInSession()
            ->filters([
                Filter::make('only_my_team')
                    ->label('Only my team')
                    ->query(function (Builder $query, array $data, $livewire = null) {

                        if ($livewire && property_exists($livewire, 'currentTeamId') && $livewire->currentTeamId) {
                            return $query->where('team_id', $livewire->currentTeamId);
                        }
                        return $query;
                    }),
                Filter::make('smart_recent')
                    ->label('Smart Recent Records')
                    ->toggle()
                    ->query(function (Request $request, Builder $query, array $data, Table $table) {
                        dd('1' || $request->query('tableFilters', []));
                        $perPage = $table->getRecordsPerPage();
                        // dd($perPage);

                        // Change logic dynamically
                        if ($perPage <= 10) {
                            $days = 1;
                        } elseif ($perPage <= 50) {
                            $days = 7;
                        } else {
                            $days = 30;
                        }

                        return $query->where('created_at', '>=', now()->subDays($days));
                    }),
                TernaryFilter::make('is_admin')
                    ->placeholder('All users')
                    ->trueLabel('Admin users')
                    ->falseLabel('Not Admin users'),
                QueryBuilder::make('custom')
                    ->constraints([
                        QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label('User Name')
                            ->icon('heroicon-o-magnifying-glass') // Add unique identifier
                            ->operators([
                                StartsWithOperator::class, // ← your custom operator

                            ]),
                        Constraint::make('subscribed')
                            ->label('Subscribed')
                            ->icon('heroicon-o-bell')
                            ->operators([
                                Operator::make('subscribed')
                                    ->label(fn(bool $isInverse): string => $isInverse ? 'Not subscribed' : 'Subscribed')
                                    ->summary(fn(bool $isInverse): string => $isInverse ? 'Users without active subscription' : 'Users with active subscription')
                                    ->baseQuery(
                                        fn(Builder $query, bool $isInverse) =>
                                        // use whereHas when normal, whereDoesntHave when inverted
                                        $query->{$isInverse ? 'whereDoesntHave' : 'whereHas'}(
                                            'subscriptions',
                                            fn(Builder $q) => $q->where('status', 'active') // adjust status as needed
                                        )
                                    ),
                            ]),

                    ])
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->actions([
                Action::make('copyToSelected')
                    ->accessSelectedRecords()
                    ->action(function (Model $record, Collection $selectedRecords) {

                        $selectedRecords->each(
                            fn(Model $selectedRecord) => $selectedRecord->update([
                                'is_admin' => $record->is_admin,
                            ]),
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('approve')
                        ->label('Approve selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {

                            $records->each(function ($record) {
                                $record->status = 'approved';
                                $record->save();
                            });
                        })
                        ->deselectRecordsAfterCompletion()

                ]),
            ])->checkIfRecordIsSelectableUsing(
                fn(Model $record): bool => $record->status === 'approved',
            )->selectCurrentPageOnly();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
