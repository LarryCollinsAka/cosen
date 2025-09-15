<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PromptController;
use Illuminate\Support\Facades\Route;




Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
   Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('prompts', PromptController::class);
});