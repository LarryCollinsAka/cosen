<?php

use App\Http\Controllers\IncidentReportController;
use App\Http\Controllers\Community\DashboardController as CommunityDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    // This GET route displays the chat form
    Route::get('/reports/chat', [IncidentReportController::class, 'chat'])->name('reports.chat');
    
    // ...existing POST routes for 'store' and 'continue-conversation'
    Route::post('/reports', [IncidentReportController::class, 'store'])->name('reports.store');
    Route::post('/reports/continue-conversation', [IncidentReportController::class, 'continueConversation']);
     // Community Dashboard Route
    Route::get('/dashboard', [CommunityDashboardController::class, 'index'])->name('community.dashboard');
});