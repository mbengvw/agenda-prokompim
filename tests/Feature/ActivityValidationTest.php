<?php

use App\Models\Activity;
use App\Models\User;

test('activity cannot be saved without start time when not tentative', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('activities.create'))
        ->followingRedirects()
        ->post(route('activities.store'), [
            'title' => 'Rapat Koordinasi',
            'activity_date' => '2026-10-28',
        ])
        ->assertOk()
        ->assertSee('Agenda belum bisa disimpan. Periksa kembali isian berikut:')
        ->assertSee('Waktu mulai wajib diisi jika kegiatan tidak bersifat tentatif.')
        ->assertSee('Rapat Koordinasi');

    expect(Activity::where('title', 'Rapat Koordinasi')->exists())->toBeFalse();
});

test('activity can be saved without times when tentative', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('activities.store'), [
            'title' => 'Agenda Tentatif',
            'activity_date' => '2026-10-28',
            'is_tentative' => '1',
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('activities', [
        'title' => 'Agenda Tentatif',
        'is_tentative' => true,
        'start_time' => null,
    ]);
});

test('activity can be saved with valid times', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('activities.store'), [
            'title' => 'Rapat Koordinasi',
            'activity_date' => '2026-10-28',
            'start_time' => '09:00',
            'end_time' => '10:00',
        ])
        ->assertSessionHasNoErrors();

    $activity = Activity::where('title', 'Rapat Koordinasi')->first();

    expect($activity)->not->toBeNull()
        ->and($activity->start_time?->format('H:i'))->toBe('09:00')
        ->and($activity->end_time?->format('H:i'))->toBe('10:00');
});

test('activity can be saved without end time', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('activities.store'), [
            'title' => 'Agenda Sampai Selesai',
            'activity_date' => '2026-10-28',
            'start_time' => '09:00',
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('activities', [
        'title' => 'Agenda Sampai Selesai',
        'start_time' => '09:00',
        'end_time' => null,
    ]);
});

test('activity can be saved with a long title', function () {
    $user = User::factory()->create();
    $title = rtrim(str_repeat('Rapat koordinasi pembangunan daerah ', 12));

    $this->actingAs($user)
        ->post(route('activities.store'), [
            'title' => $title,
            'activity_date' => '2026-10-28',
            'start_time' => '09:00',
        ])
        ->assertSessionHasNoErrors();

    expect(Activity::where('title', $title)->exists())->toBeTrue();
});

test('expired session is redirected back with a friendly message', function () {
    $user = User::factory()->create();

    $this->actingAs($user);
    $this->app['env'] = 'production';

    $this->from(route('activities.create'))
        ->followingRedirects()
        ->post('/activities', ['title' => 'Rapat Koordinasi'])
        ->assertOk()
        ->assertSee('Sesi Anda telah berakhir. Silakan muat ulang halaman lalu coba lagi.');

    expect(Activity::where('title', 'Rapat Koordinasi')->exists())->toBeFalse();

    $this->app['env'] = 'testing';
});
