<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityDispositionController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProtocolOfficerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $actionRequiredActivities = \App\Models\Activity::with(['leader', 'dispositions'])
        ->where(function ($q) {
            $q->where('status', 'submitted')
              ->orWhere(function ($q2) {
                  $q2->where('status', 'approved')
                     ->whereNotNull('leader_id')
                     ->whereDoesntHave('dispositions', function ($q3) {
                         $q3->whereColumn('from_leader_id', 'activities.leader_id');
                     });
              });
        })
        ->orderBy('activity_date', 'asc')
        ->orderBy('start_time', 'asc')
        ->get();

    return view('dashboard', compact('actionRequiredActivities'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Master Data - Read Only for all auth
    Route::resource('leaders', LeaderController::class)->only(['index', 'show', 'store']);
    Route::resource('locations', LocationController::class)->only(['index', 'show', 'store']);
    Route::resource('organizations', OrganizationController::class)->only(['index', 'show', 'store']);
    Route::resource('protocol-officers', ProtocolOfficerController::class)->parameters([
        'protocol-officers' => 'protocolOfficer',
    ])->only(['index', 'show', 'store']);

    // Master Data - Write for Admin Only
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('leaders', LeaderController::class)->except(['index', 'show', 'store']);
        Route::resource('locations', LocationController::class)->except(['index', 'show', 'store']);
        Route::resource('organizations', OrganizationController::class)->except(['index', 'show', 'store']);
        Route::resource('protocol-officers', ProtocolOfficerController::class)->parameters([
            'protocol-officers' => 'protocolOfficer',
        ])->except(['index', 'show', 'store']);
        
        // User & Role Management
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::resource('roles', RoleController::class);
    });

    Route::get('activities/export-pdf', [ActivityController::class, 'exportPdf'])->name('activities.export-pdf');
    Route::resource('activities', ActivityController::class);
    Route::patch('activities/{activity}/status', [ActivityController::class, 'updateStatus'])->name('activities.update-status');
    Route::post('activities/{activity}/disposition', [ActivityDispositionController::class, 'store'])->name('activities.disposition');
});

require __DIR__.'/auth.php';
