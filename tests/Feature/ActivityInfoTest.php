<?php

use App\Models\Activity;
use App\Models\Leader;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->bupati = Leader::create([
        'name' => 'Bupati Test',
        'position' => 'bupati',
        'hierarchy_level' => 1,
        'is_active' => true,
    ]);

    $this->wabup = Leader::create([
        'name' => 'Wabup Test',
        'position' => 'wakil bupati',
        'hierarchy_level' => 2,
        'is_active' => true,
    ]);
});

test('info is saved when creating an activity and copied to companion child activities', function () {
    $this->actingAs($this->user);

    $response = $this->post(route('activities.store'), [
        'title' => 'Rapat Paripurna DPRD',
        'activity_date' => '2026-10-15',
        'is_tentative' => 0,
        'start_time' => '09:00',
        'end_time' => '11:00',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id],
        'info' => 'Membawa dokumen laporan tahunan.',
    ]);

    $response->assertRedirect();

    $parentActivity = Activity::whereNull('parent_activity_id')
        ->where('leader_id', $this->bupati->id)
        ->first();

    expect($parentActivity->info)->toBe('Membawa dokumen laporan tahunan.');

    $wabupChild = Activity::where('parent_activity_id', $parentActivity->id)
        ->where('leader_id', $this->wabup->id)
        ->first();

    expect($wabupChild->info)->toBe('Membawa dokumen laporan tahunan.');
});

test('info can be updated and the change propagates to companion child activities', function () {
    $this->actingAs($this->user);

    $this->post(route('activities.store'), [
        'title' => 'Kunjungan Kerja',
        'activity_date' => '2026-10-16',
        'is_tentative' => 0,
        'start_time' => '08:00',
        'end_time' => '10:00',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id],
        'info' => 'Info awal.',
    ]);

    $parentActivity = Activity::whereNull('parent_activity_id')->first();

    $response = $this->put(route('activities.update', $parentActivity), [
        'title' => 'Kunjungan Kerja',
        'activity_date' => '2026-10-16',
        'is_tentative' => 0,
        'start_time' => '08:00',
        'end_time' => '10:00',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id],
        'info' => 'Info diperbarui.',
    ]);

    $response->assertRedirect();

    $parentActivity->refresh();
    expect($parentActivity->info)->toBe('Info diperbarui.');

    $wabupChild = Activity::where('parent_activity_id', $parentActivity->id)
        ->where('leader_id', $this->wabup->id)
        ->first();

    expect($wabupChild->info)->toBe('Info diperbarui.');
});

test('info is optional and defaults to null', function () {
    $this->actingAs($this->user);

    $this->post(route('activities.store'), [
        'title' => 'Kegiatan Tanpa Info',
        'activity_date' => '2026-10-17',
        'is_tentative' => 0,
        'start_time' => '13:00',
        'end_time' => '14:00',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [],
    ]);

    $parentActivity = Activity::whereNull('parent_activity_id')->first();

    expect($parentActivity->info)->toBeNull();
});
