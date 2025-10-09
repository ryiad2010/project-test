<?php

namespace App\Filament\Clusters\Mycluster\Resources\SubscriptionResource\Pages;

use App\Filament\Clusters\Mycluster\Resources\SubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;
}
