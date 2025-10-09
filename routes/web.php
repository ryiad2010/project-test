
<?php

use App\Components\TextInput;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Filament\CacheController;
use App\Livewire\DemoForm;
use App\Livewire\TestForm;

Route::post('/filament/clear-cache', [CacheController::class, 'clear'])
    ->name('filament.clear-cache');

/*     Route::get(
        '/admin/dashboard',
        \App\Http\Livewire\AdminDashboard::class
    )->name('filament.admin.dashboard');

 */
Route::get('/demo', TestForm::class);
Route::get('/demo2',DemoForm::class);
