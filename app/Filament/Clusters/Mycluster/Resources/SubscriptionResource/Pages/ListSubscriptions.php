<?php

namespace App\Filament\Clusters\Mycluster\Resources\SubscriptionResource\Pages;

use App\Filament\Clusters\Mycluster\Resources\SubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
