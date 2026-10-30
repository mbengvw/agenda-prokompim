<?php

use App\Models\Activity;
use App\Models\Leader;
use App\Models\User;
use Spatie\Permission\Models\Role;

test('sekpri role creates activity with submitted status', function (string $role) {
    Role::firstOrCreate(['name' => $role]);

    $user = User::factory()->create();
    $user->assignRole($role);

    $leader = Leader::create([
        'name' => 'Bupati Test',
        'position' => 'bupati',
        'hierarchy_level' => 1,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->post(route('activities.store'), [
            'title' => 'Rapat Koordinasi Sekpri',
            'activity_date' => '2026-10-28',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'leader_id' => $leader->id,
        ])
        ->assertSessionHasNoErrors();

    $activity = Activity::where('title', 'Rapat Koordinasi Sekpri')->first();

    expect($activity)->not->toBeNull()
        ->and($activity->status)->toBe('submitted');
})->with([
    'sekpri_bupati',
    'sekpri_wabup',
    'sekpri_sekda',
]);

test('user without ajudan or sekpri role creates activity with draft status', function () {
    Role::firstOrCreate(['name' => 'staf_protokol']);

    $user = User::factory()->create();
    $user->assignRole('staf_protokol');

    $leader = Leader::create([
        'name' => 'Bupati Test',
        'position' => 'bupati',
        'hierarchy_level' => 1,
        'is_active' => true,
    ]);

    $this->actingAs($user)
        ->post(route('activities.store'), [
            'title' => 'Rapat Koordinasi Staf',
            'activity_date' => '2026-10-28',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'leader_id' => $leader->id,
        ])
        ->assertSessionHasNoErrors();

    expect(Activity::where('title', 'Rapat Koordinasi Staf')->first()?->status)->toBe('draft');
});
