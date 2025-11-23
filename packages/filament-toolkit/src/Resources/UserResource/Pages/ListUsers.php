<?php

namespace Ryiad\FilamentToolkit\Resources\UserResource\Pages;

use Ryiad\FilamentToolkit\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
    public ?int $currentTeamId = null;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function mount(): void
    {
        parent::mount();

        $this->currentTeamId = auth()->user()?->id;
    }
}
