<?php

namespace Ryiad\FilamentToolkit\Resources;

use Ryiad\FilamentToolkit\Resources\UserResource\Pages;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Table;


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
            ->columns([
                Tables\Columns\TextColumn::make('name')
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
                            })
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
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                //
            ])
            ->actions([
                //     Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
