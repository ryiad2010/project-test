<?php

namespace App\Http\Controllers\Filament;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;

class CacheController extends Controller
{
    public function clear(): RedirectResponse
    {
      
        Artisan::call('cache:clear');

        // Fire a Filament toast notification
        Notification::make()
            ->title('Application cache cleared successfully.')
            ->success()
            ->send();

        // Redirect back to the Filament panel
        return redirect()->back();
    }
}
