<?php

namespace App\Filament\Resources;

use App\Models\Student;
use Filament\Resources\Resource;
use App\Filament\Resources\StudentResource\Pages;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
// ✅ CORRECT imports for TABLE layout
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

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
                                    ->extraFieldWrapperAttributes(['class' => 'components-locked'])
                                    ->helperText(new HtmlString('Your <strong>full name</strong> here, including any middle names.'))
                                    ->required()
                                    ->autocapitalize('words')
                                    ->maxLength(255),
                                TextInput::make('manufacturer')
                                    ->datalist([
                                        'BMW',
                                        'Ford',
                                        'Mercedes-Benz',
                                        'Porsche',
                                        'Toyota',
                                        'Tesla',
                                        'Volkswagen',
                                    ])->dehydrated(false),
                                TextInput::make('backgroundColor')
                                    ->type('color')
                                    ->dehydrated(false),

                                TextInput::make('email')
                                    ->helperText(str('Your **full name** here, including any middle names.')->inlineMarkdown()->toHtmlString())
                                    ->prefix('https://')
                                    ->suffix('.com')
                                    ->suffixIcon('heroicon-m-globe-alt')
                                    ->suffixIconColor('success')
                                    ->email()
                                    ->required(),

                                TextInput::make('phone')
                                    ->hint(new HtmlString('<a href="/forgotten-password">Forgotten your password?</a>'))
                                    ->hintColor('danger')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Need some more information?')
                                    ->tel(),

                                TextInput::make('rate')
                                    ->numeric()
                                    ->mask(RawJs::make('$money($input)'))

                                    ->step(100)
                                    ->prefix('$') // optional
                                    ->label('Rate')
                                    ->autocomplete('score')
                                    ->nullable(),
                                TextInput::make('score')
                                    ->numeric()
                                    ->inputMode('numeric')
                                    ->label('score')
                                    ->nullable(),
                                Select::make('technology')
                                    ->options([
                                        'tailwind' => '<span class="text-blue-500">Tailwind</span>',
                                        'alpine' => '<span class="text-green-500">Alpine</span>',
                                        'laravel' => '<span class="text-red-500">Laravel</span>',
                                        'livewire' => '<span class="text-pink-500">Livewire</span>',
                                    ])

                                    ->searchable()
                                    ->allowHtml(),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'reviewing' => 'Reviewing',
                                        'published' => 'Published',
                                    ])
                                    ->default('draft')
                                    ->disableOptionWhen(fn(string $value): bool => $value === 'published')
                                    ->in(fn(Select $component): array => array_keys($component->getEnabledOptions()))
                                    ->selectablePlaceholder(false),


                            ]),

                        // RIGHT SECTION
                        Section::make('Course Information')
                            ->schema([
                                TextInput::make('course_name')
                                    ->required(),

                                DatePicker::make('start_date')
                                    ->required(),
                                DatePicker::make('end_date'),
                                TextInput::make('birthday')
                                    ->mask('99/99/9999')
                                    ->placeholder('MM/DD/YYYY')
                                    ->dehydrated(false),
                                TextInput::make('cardNumber')
                                    ->mask(RawJs::make(<<<'JS'
        $input.startsWith('34') || $input.startsWith('37') ? '9999 999999 99999' : '9999 9999 9999 9999'
    JS))
                                    ->dehydrated(false),

                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Students')
            ->description('Manage your Students here.')
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
                            Average::make()->label('Summaries Average')->numeric(decimalPlaces: 2, locale: 'ar'),
                            Range::make()->label('Summaries Range'),
                            Sum::make()->money('EUR')->label('Summaries Sum')->prefix('Total volume: ')
                                ->suffix(new HtmlString(' m&sup3;')),
                        ])->visible(fn(Builder $query): bool => $query->exists()),
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

            ])->recordUrl(
                fn(Model $record) =>
                static::getUrl('view', ['record' => $record])
            )

            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit'   => Pages\EditStudent::route('/{record}/edit'),
            'view'   => Pages\EditStudent::route('/{record}/view'),
        ];
    }
}
