<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProtocolOfficerController;
use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('leaders', LeaderController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('organizations', OrganizationController::class);
    Route::resource('protocol-officers', ProtocolOfficerController::class)->parameters([
        'protocol-officers' => 'protocolOfficer'
    ]);
    Route::resource('activities', ActivityController::class);
    Route::patch('activities/{activity}/status', [ActivityController::class, 'updateStatus'])->name('activities.update-status');
});

require __DIR__.'/auth.php';
