<?php

use App\Http\Controllers\Api\TestUserController;
use Illuminate\Support\Facades\Route;

Route::post('/users', [TestUserController::class, 'store']);

