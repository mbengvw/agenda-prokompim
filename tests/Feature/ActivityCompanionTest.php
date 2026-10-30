<?php

use App\Models\Activity;
use App\Models\Leader;
use App\Models\User;
use App\Services\ActivityService;
use Spatie\Permission\Models\Role;

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

    $this->sekda = Leader::create([
        'name' => 'Sekda Test',
        'position' => 'sekda',
        'hierarchy_level' => 3,
        'is_active' => true,
    ]);
});

test('creating an activity with companions automatically creates companion child activities', function () {
    $this->actingAs($this->user);

    $response = $this->post(route('activities.store'), [
        'title' => 'Rapat Paripurna DPRD',
        'activity_date' => '2026-10-15',
        'is_tentative' => 0,
        'start_time' => '09:00',
        'end_time' => '11:00',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id, $this->sekda->id],
    ]);

    $response->assertRedirect();

    $parentActivity = Activity::whereNull('parent_activity_id')
        ->where('leader_id', $this->bupati->id)
        ->first();

    expect($parentActivity)->not->toBeNull();
    expect($parentActivity->companions)->toHaveCount(2);

    $wabupChild = Activity::where('parent_activity_id', $parentActivity->id)
        ->where('leader_id', $this->wabup->id)
        ->first();

    $sekdaChild = Activity::where('parent_activity_id', $parentActivity->id)
        ->where('leader_id', $this->sekda->id)
        ->first();

    expect($wabupChild)->not->toBeNull();
    expect($wabupChild->title)->toBe('Mendampingi bupati dalam kegiatan Rapat Paripurna DPRD');

    expect($sekdaChild)->not->toBeNull();
    expect($sekdaChild->title)->toBe('Mendampingi bupati dalam kegiatan Rapat Paripurna DPRD');
});

test('removing a companion during activity edit deletes their companion child activity', function () {
    $this->actingAs($this->user);

    // 1. Create initial activity with both Wabup and Sekda as companions
    $this->post(route('activities.store'), [
        'title' => 'Kunjungan Kerja Lapangan',
        'activity_date' => '2026-10-16',
        'is_tentative' => 0,
        'start_time' => '08:00',
        'end_time' => '10:00',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id, $this->sekda->id],
    ]);

    $parentActivity = Activity::whereNull('parent_activity_id')
        ->where('leader_id', $this->bupati->id)
        ->first();

    $sekdaChild = Activity::where('parent_activity_id', $parentActivity->id)
        ->where('leader_id', $this->sekda->id)
        ->first();
    expect($sekdaChild)->not->toBeNull();

    // 2. Edit activity to remove Sekda (leaving only Wabup)
    $response = $this->put(route('activities.update', $parentActivity), [
        'title' => 'Kunjungan Kerja Lapangan',
        'activity_date' => '2026-10-16',
        'is_tentative' => 0,
        'start_time' => '08:00',
        'end_time' => '10:00',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id],
    ]);

    $response->assertRedirect();

    // Verify parent's companions pivot only has Wabup
    $parentActivity->refresh();
    expect($parentActivity->companions->pluck('id')->toArray())->toEqual([$this->wabup->id]);

    // Verify Wabup child activity still exists
    $wabupChild = Activity::where('parent_activity_id', $parentActivity->id)
        ->where('leader_id', $this->wabup->id)
        ->first();
    expect($wabupChild)->not->toBeNull();

    // Verify Sekda child activity has been deleted (soft-deleted)
    $sekdaChildActive = Activity::where('parent_activity_id', $parentActivity->id)
        ->where('leader_id', $this->sekda->id)
        ->first();
    expect($sekdaChildActive)->toBeNull();

    $sekdaChildTrashed = Activity::withTrashed()
        ->where('parent_activity_id', $parentActivity->id)
        ->where('leader_id', $this->sekda->id)
        ->first();
    expect($sekdaChildTrashed)->not->toBeNull();
    expect($sekdaChildTrashed->trashed())->toBeTrue();
});

test('re-adding a previously removed companion restores and updates the child activity', function () {
    $this->actingAs($this->user);

    $this->post(route('activities.store'), [
        'title' => 'Apel Pagi Gabungan',
        'activity_date' => '2026-10-17',
        'is_tentative' => 0,
        'start_time' => '07:30',
        'end_time' => '08:30',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id, $this->sekda->id],
    ]);

    $parentActivity = Activity::whereNull('parent_activity_id')->first();

    // Remove Sekda
    $this->put(route('activities.update', $parentActivity), [
        'title' => 'Apel Pagi Gabungan',
        'activity_date' => '2026-10-17',
        'is_tentative' => 0,
        'start_time' => '07:30',
        'end_time' => '08:30',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id],
    ]);

    expect(Activity::where('parent_activity_id', $parentActivity->id)->where('leader_id', $this->sekda->id)->exists())->toBeFalse();

    // Re-add Sekda with updated title
    $this->put(route('activities.update', $parentActivity), [
        'title' => 'Apel Pagi Gabungan Bersama Seluruh OPD',
        'activity_date' => '2026-10-17',
        'is_tentative' => 0,
        'start_time' => '07:30',
        'end_time' => '08:30',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id, $this->sekda->id],
    ]);

    $restoredSekdaChild = Activity::where('parent_activity_id', $parentActivity->id)
        ->where('leader_id', $this->sekda->id)
        ->first();

    expect($restoredSekdaChild)->not->toBeNull();
    expect($restoredSekdaChild->trashed())->toBeFalse();
    expect($restoredSekdaChild->title)->toBe('Mendampingi bupati dalam kegiatan Apel Pagi Gabungan Bersama Seluruh OPD');
});

test('updating parent activity details updates existing child activities', function () {
    $this->actingAs($this->user);

    $this->post(route('activities.store'), [
        'title' => 'Rapat Awal',
        'activity_date' => '2026-10-18',
        'is_tentative' => 0,
        'start_time' => '08:00',
        'end_time' => '09:00',
        'location_text' => 'Ruang Kerja Bupati',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id],
    ]);

    $parentActivity = Activity::whereNull('parent_activity_id')->first();

    $this->put(route('activities.update', $parentActivity), [
        'title' => 'Rapat Revisi',
        'activity_date' => '2026-10-19',
        'is_tentative' => 0,
        'start_time' => '10:00',
        'end_time' => '12:00',
        'location_text' => 'Pendopo Kabupaten',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id],
    ]);

    $wabupChild = Activity::where('parent_activity_id', $parentActivity->id)
        ->where('leader_id', $this->wabup->id)
        ->first();

    expect($wabupChild->title)->toBe('Mendampingi bupati dalam kegiatan Rapat Revisi');
    expect($wabupChild->activity_date->toDateString())->toBe('2026-10-19');
    expect($wabupChild->start_time->format('H:i'))->toBe('10:00');
    expect($wabupChild->location_text)->toBe('Pendopo Kabupaten');
});

test('deleting parent activity deletes all child activities', function () {
    $this->actingAs($this->user);

    $service = app(ActivityService::class);
    $parent = $service->createActivity([
        'title' => 'Kegiatan Utama',
        'activity_date' => '2026-10-20',
        'is_tentative' => true,
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id, $this->sekda->id],
    ]);

    expect(Activity::where('parent_activity_id', $parent->id)->count())->toBe(2);

    $service->deleteActivity($parent);

    expect(Activity::where('parent_activity_id', $parent->id)->count())->toBe(0);
    expect(Activity::withTrashed()->where('parent_activity_id', $parent->id)->count())->toBe(2);
});

test('kabag_protokol can cancel approval on an approved activity and status returns to submitted', function () {
    Role::firstOrCreate(['name' => 'kabag_protokol']);
    $this->user->assignRole('kabag_protokol');
    $this->actingAs($this->user);

    $service = app(ActivityService::class);
    $parent = $service->createActivity([
        'title' => 'Kegiatan Disetujui',
        'activity_date' => '2026-10-21',
        'is_tentative' => true,
        'status' => 'approved',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id],
    ]);

    $child = Activity::where('parent_activity_id', $parent->id)->first();
    expect($parent->status)->toBe('approved');
    expect($child->status)->toBe('approved');

    $response = $this->patch(route('activities.update-status', $parent->id), [
        'status' => 'submitted',
    ]);

    $response->assertRedirect();

    $parent->refresh();
    $child->refresh();

    expect($parent->status)->toBe('submitted');
    expect($child->status)->toBe('submitted');
});

test('whatsapp export uses parent title for companion activities and marks them as disposition', function () {
    $this->actingAs($this->user);

    $this->post(route('activities.store'), [
        'title' => 'Rapat Paripurna DPRD',
        'activity_date' => '2026-10-15',
        'is_tentative' => 0,
        'start_time' => '09:00',
        'end_time' => '11:00',
        'leader_id' => $this->bupati->id,
        'companion_ids' => [$this->wabup->id],
    ]);

    $response = $this->getJson(route('activities.export-whatsapp', [
        'leader_id' => $this->wabup->id,
        'date' => '2026-10-15',
    ]));

    $response->assertOk();

    $text = $response->json('text');

    expect($text)
        ->toContain('*Rapat Paripurna DPRD*')
        ->not->toContain('*Mendampingi bupati dalam kegiatan Rapat Paripurna DPRD*')
        ->toContain('Mendampingi Bupati');
});
