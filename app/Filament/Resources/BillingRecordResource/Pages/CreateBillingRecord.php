<?php

namespace App\Filament\Resources\BillingRecordResource\Pages;

use App\Filament\Resources\BillingRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBillingRecord extends CreateRecord
{
    protected static string $resource = BillingRecordResource::class;
       protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
