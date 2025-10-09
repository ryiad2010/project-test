<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\Mycluster;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\View\View;


class Settings extends Page
{


    protected static ?string $navigationGroup = 'Setting Group';

    protected static ?string $cluster = Mycluster::class;


    public static function getNavigationLabel(): string
    {
        return 'Settings';
    }



    protected static string $view = 'filament.pages.settings';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    public function getExtraBodyAttributes(): array
    {
        return [
            'id' => 'custom-page',
            'data-theme' => 'dark',
            'class' => 'custom-body-class',
        ];
    }

    public  function getHeader(): ?View
    {
        //   dd(Settings::getUrl(panel: 'marketing'));
        //  dd(Settings::getUrl(['section' => 'notifications']));
        return view('components.custom-header', [
            'Var' => Settings::getUrl(panel: 'marketing')
        ]);
    }
    public function getFooter(): ?View
    {
        return view('components.custom-footer');
    }
}
