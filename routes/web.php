<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/home');
});

Route::get('/home', [DashboardController::class, 'index']);