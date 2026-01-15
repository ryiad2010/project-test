<?php

namespace App\Filament\Resources;

use App\Models\Student;
use Filament\Resources\Resource;
use App\Filament\Resources\StudentResource\Pages;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
// ✅ CORRECT imports for TABLE layout
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\TextColumn;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Academics';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([

                        // LEFT SECTION
                        Section::make('Student Information')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->email()
                                    ->required(),

                                TextInput::make('phone')
                                    ->tel(),

                                TextInput::make('rate')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('$') // optional
                                    ->label('Rate')
                                    ->nullable(),
                                TextInput::make('score')
                                    ->numeric()
                                    ->label('score')
                                    ->nullable(),
                            ]),

                        // RIGHT SECTION
                        Section::make('Course Information')
                            ->schema([
                                TextInput::make('course_name')
                                    ->required(),

                                DatePicker::make('start_date')
                                    ->required(),

                                DatePicker::make('end_date'),
                            ]),
                    ]),
            ]);
    }

    // -------------------------------------------------
    // TABLE (Panel + Stack)
    // -------------------------------------------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Split::make([
                    TextColumn::make('name')
                        ->label('Name')
                        ->weight('bold')
                        ->summarize(Range::make()->minimalTextualDifference())
                        ->searchable(),

                    TextColumn::make('email')
                        ->label('Email')
                        ->color('gray')
                        ->searchable(),

                    TextColumn::make('phone')
                        ->label('Phone')
                        ->placeholder('-'),
                    TextColumn::make('rate')
                        ->money('USD') // or remove if not currency
                        ->sortable()
                        ->summarize([
                            Average::make()->label('Summaries Average'),
                            Range::make()->label('Summaries Range'),
                        ]),
                    TextColumn::make('created_at')
                        ->dateTime()
                        ->summarize(Range::make()->minimalDateTimeDifference()),

                    TextColumn::make('score')
                        ->numeric()
                        ->sortable(),

                ]),

                // -------- RIGHT CARD (Course Info) --------
                Panel::make([
                    Stack::make([
                        TextColumn::make('course_name')
                            ->label('Course')
                            ->badge(),

                        TextColumn::make('start_date')
                            ->label('Start Date')
                            ->date(),

                        TextColumn::make('end_date')
                            ->label('End Date')
                            ->date()
                            ->placeholder('-'),
                    ])->space(2),
                ])->collapsible(),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    // -------------------------------------------------
    // PAGES
    // -------------------------------------------------
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit'   => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
