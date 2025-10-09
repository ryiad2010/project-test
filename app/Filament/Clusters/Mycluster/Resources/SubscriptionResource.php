<?php

namespace App\Filament\Clusters\Mycluster\Resources;

use App\Filament\Clusters\Mycluster;
use App\Filament\Clusters\Mycluster\Resources\SubscriptionResource\Pages;
use App\Filament\Clusters\Mycluster\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Mycluster::class;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('user_id')->label('User ID')->required(),
            TextInput::make('stripe_subscription_id')->label('Stripe Subscription ID')->required(),
            Select::make('status')->label('Status')->options([
                'active' => 'Active',
                'canceled' => 'Canceled',
                'past_due' => 'Past Due',
            ])->required(),
            DateTimePicker::make('trial_ends_at')->label('Trial Ends At'),
            DateTimePicker::make('ends_at')->label('Ends At'),
        ]);
}


    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('user.name')->label('User'),
            TextColumn::make('stripe_subscription_id')->label('Stripe ID'),
            BadgeColumn::make('status')->label('Status')->colors([
                'success' => 'active',
                'warning' => 'past_due',
                'danger' => 'canceled',
            ]),
            TextColumn::make('trial_ends_at')->date(),
            TextColumn::make('ends_at')->date(),
            TextColumn::make('created_at')->label('Created')->dateTime(),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
