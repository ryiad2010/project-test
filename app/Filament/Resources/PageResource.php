<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Form;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TagsInput;
use Illuminate\Support\Facades\Date;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Set;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Page Details')
                    ->columns([
                        'sm' => 3,
                        'xl' => 6,
                        '2xl' => 8,
                    ])->schema([
                        TextInput::make('age')
                            ->numeric()       // ensures the input is a number
                            ->multipleOf(2)
                            ->suffixAction(
                                Action::make('copyCostToPrice')
                                    ->icon('heroicon-m-clipboard')
                                    ->requiresConfirmation()
                                    ->action(function (Set $set, $state) {
                                        $set('price', $state);
                                    })
                            )
                            ->hintAction(
                                Action::make('copyCostToPrice')
                                    ->icon('heroicon-m-clipboard')
                                    ->requiresConfirmation()
                                    ->action(function (Set $set, $state) {
                                        $set('price', $state);
                                    })
                            ),
                        TextInput::make('title')
                            ->label('Page Title')
                            ->required()
                            ->columnStart([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ]),
                    ]),
                TextInput::make('website')
                    ->label('Website URL')
                    ->activeUrl()
                    ->required(),
                DatePicker::make('start_date')->after('tomorrow'),

                TagsInput::make('tags')
                    ->suggestions([
                        'tailwindcss',
                        'alpinejs',
                        'laravel',
                        'livewire',
                    ])->splitKeys(['Tab', ' '])->tagSuffix('%')->reorderable()->color('danger')->dehydrated(false),
                Textarea::make('description')
                    //  ->rows(10)
                    // ->cols(20)
                    ->autosize()
                    ->dehydrated(false),
                KeyValue::make('meta'),


                Builder::make('content')
                    ->blocks([
                        Builder\Block::make('heading')
                            ->schema([
                                TextInput::make('content')
                                    ->label('Heading')
                                    ->required(),
                                Select::make('level')
                                    ->label('Heading Level')
                                    ->options([
                                        'h1' => 'Heading 1',
                                        'h2' => 'Heading 2',
                                        'h3' => 'Heading 3',
                                        'h4' => 'Heading 4',
                                        'h5' => 'Heading 5',
                                        'h6' => 'Heading 6',
                                    ])
                                    ->required(),
                            ])
                            ->columns(2),

                        Builder\Block::make('paragraph')
                            ->schema([
                                Textarea::make('content')
                                    ->label('Paragraph')
                                    ->required(),
                            ]),

                        Builder\Block::make('image')
                            ->schema([
                                FileUpload::make('url')
                                    ->label('Image')
                                    ->image()
                                    ->required(),
                                TextInput::make('alt')
                                    ->label('Alt Text')
                                    ->required(),
                            ]),
                    ])->blockNumbers(false)->reorderableWithButtons(),

                ColorPicker::make('color')->rgba()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Title')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
