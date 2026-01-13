<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillingRecordResource\Pages;
use App\Models\BillingRecord;
use App\Models\User;
use App\Services\BillingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;

class BillingRecordResource extends Resource
{
    protected static ?string $model = BillingRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Billing';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->default(function () {
                                return app(BillingService::class)->generateInvoiceNumber();
                            })
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->default('pending')
                            ->required(),

                        Forms\Components\TextInput::make('currency')
                            ->default('USD')
                            ->maxLength(3)
                            ->required(),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('Due Date')
                            ->displayFormat('d/m/Y')
                            ->native(false)

                            ->default(now()->addDays(30)),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Line Items')
                    ->schema([
                        Forms\Components\Repeater::make('line_items')
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->required()
                                    ->columnSpan(2),


                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),

                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required(),
                            ])
                            ->columns(4)
                            ->addActionLabel('Add Line Item')
                            ->live()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $lineItems = $get('line_items') ?? [];
                                $total = collect($lineItems)->sum(
                                    fn($item) => ($item['quantity'] ?? 0) * ($item['price'] ?? 0)
                                );
                                $set('amount', $total);
                            }),
                    ]),

                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Total Amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('payment_method')
                            ->label('Payment Method'),

                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID'),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Paid At'),

                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\Textarea::make('description2')
                            ->rows(3),



                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                tables\Columns\TextColumn::make('amount')
                    ->label('amount2')
                    ->money('YER', divideBy: 100, locale: 'EN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('formatted_amount')
                    ->label('Amount')

                    ->sortable('amount'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                        'secondary' => 'refunded',
                    ]),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->color('danger')

                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->bulleted()
                    ->badge()
                    ->separator(','),


                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->formatStateUsing(fn(string $state): View => view(
                        'filament.tables.columns.description-entry-content',
                        ['state' => $state],
                    )),
                Tables\Columns\TextColumn::make('description2')
                    ->label('Description2')
                    ->markdown()
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),

                Tables\Filters\Filter::make('overdue')
                    ->query(
                        fn(Builder $query): Builder => $query
                            ->where('status', 'pending')
                            ->where('due_date', '<', now())
                    ),
            ])->deferFilters()
            ->filtersApplyAction(
                fn(Action $action) => $action
                    ->link()
                    ->label('Save filters to table'),
            )
            ->deselectAllRecordsWhenFiltered(false)
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('mark_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(BillingRecord $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->required(),
                    ])
                    ->action(function (BillingRecord $record, array $data) {
                        $record->markAsPaid($data['transaction_id']);

                        Notification::make()
                            ->title('Payment Recorded')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('refund')
                    ->label('Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->visible(fn(BillingRecord $record) => $record->status === 'paid')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Refund Amount')
                            ->helperText('Leave empty to refund the full amount'),
                    ])
                    ->action(function (BillingRecord $record, array $data) {
                        $billingService = app(BillingService::class);
                        $result = $billingService->refundPayment(
                            $record,
                            $data['amount'] ?? null
                        );

                        if ($result['success']) {
                            Notification::make()
                                ->title('Refund Processed')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Refund Failed')
                                ->body($result['error'])
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBillingRecords::route('/'),
            'create' => Pages\CreateBillingRecord::route('/create'),
            'edit' => Pages\EditBillingRecord::route('/{record}/edit'),
            'view' => Pages\ViewBillingRecord::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user']);
    }
}
