<?php

namespace App\Filament\Clusters\Mycluster\Resources\SubscriptionResource\Pages;

use App\Filament\Clusters\Mycluster\Resources\SubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscription extends EditRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
