<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ContentTabPosition;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;
    public $defaultAction = 'confirmPublish';


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
    public function getContentTabIcon(): ?string
    {
        return 'heroicon-m-cog';
    }
    public function getContentTabPosition(): ?ContentTabPosition
    {
        return ContentTabPosition::After;
    }

    public function confirmPublishAction(): Action
    {
        return Action::make('confirmPublish')
            ->modalHeading('Confirm Publish')
            ->color('success')
            ->action(function () {


                $this->record->update(['content' => 'published']);
                $this->refreshFormData([
                    'content',
                ]);

                Notification::make('Post published successfully!')->success()->send();
            })
            ->visible(fn() => ! ($this->record->content == 'published'));
    }
}
