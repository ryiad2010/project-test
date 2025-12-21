<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Mycluster;
use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\PostResource\RelationManagers\TagsRelationManager;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationBadgeTooltip = 'The number of users';
    protected static ?string $navigationIcon = 'heroicon-o-pencil';
    // Customize the label
    protected static ?string $navigationLabel = 'Blog Posts';
    // Control sort order
    protected static ?int $navigationSort = 2;
    // Group under "Content"
    protected static ?string $navigationGroup = 'Content';


    protected static ?string $cluster = Mycluster::class;

    // Add a badge showing post count


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(function ($state, $operation) {
                        return $operation;
                    })
                    ->required()
                    ->live(onBlur: true)
                    ->maxLength(255),
                Forms\Components\Textarea::make('content')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('comments.body')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limitList(2)
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('comments_count')->counts('comments'),
                Tables\Columns\TextColumn::make('comments_exists')->exists('comments'),
                Tables\Columns\TextColumn::make('comments_max_id')->max('comments', 'id'),

            ])
            ->filters([
                Filter::make('is_featured'),
                //  ->modifyFormFieldUsing(fn(Checkbox $field) => $field->inline(false)),
                Filter::make('created_at'),
                Filter::make('title'),


            ])
            ->filtersFormColumns(2)
            ->filtersFormSchema(fn(array $filters): array => [
                Section::make('Visibility')
                    ->description('These filters affect the visibility of the records in the table.')
                    ->schema([
                        $filters['is_featured'],
                        $filters['created_at'],
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                $filters['title'],
            ])

            ->actions([
                //   Tables\Actions\EditAction::make(),
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
            RelationGroup::make('Post Details', [
                CommentsRelationManager::class,
                TagsRelationManager::class,
            ])->badgeColor('danger')->badge('New Detials')->badgeTooltip('There are new posts')



        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
