<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = \App\Models\User::first();
auth()->login($user);

$html = view('activities.index')->with([
    'activities' => \App\Models\Activity::paginate(10),
    'timelineData' => [],
    'search' => '',
    'leaderFilter' => '',
    'dateFilter' => '2026-10-06',
    'statusFilter' => '',
    'leaders' => \App\Models\Leader::all(),
    'locations' => \App\Models\Location::all(),
    'organizations' => \App\Models\Organization::all(),
    'protocolOfficers' => \App\Models\ProtocolOfficer::all()
])->render();
