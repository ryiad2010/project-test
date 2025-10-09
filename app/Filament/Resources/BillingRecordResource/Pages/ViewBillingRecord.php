<?php
namespace App\Filament\Resources\BillingRecordResource\Pages;

use App\Filament\Resources\BillingRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBillingRecord extends ViewRecord
{
    protected static string $resource = BillingRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
