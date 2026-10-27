<?php

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
});

test('table pdf export supports daily period', function () {
    $this->actingAs($this->user)
        ->get(route('activities.export-pdf-table', [
            'leader_id' => $this->bupati->id,
            'date' => '2026-10-27',
            'period' => 'harian',
        ]))
        ->assertOk()
        ->assertDownload('Tabel_Agenda_Bupati_Test_harian_2026-10-27.pdf');
});

test('table pdf export supports weekly period', function () {
    $this->actingAs($this->user)
        ->get(route('activities.export-pdf-table', [
            'leader_id' => $this->bupati->id,
            'date' => '2026-10-27',
            'period' => 'mingguan',
        ]))
        ->assertOk()
        ->assertDownload('Tabel_Agenda_Bupati_Test_mingguan_2026-10-27.pdf');
});

test('table pdf export supports monthly period', function () {
    $this->actingAs($this->user)
        ->get(route('activities.export-pdf-table', [
            'leader_id' => $this->bupati->id,
            'date' => '2026-10-27',
            'period' => 'bulanan',
        ]))
        ->assertOk()
        ->assertDownload('Tabel_Agenda_Bupati_Test_bulanan_2026-10-27.pdf');
});

test('table pdf export supports yearly period', function () {
    $this->actingAs($this->user)
        ->get(route('activities.export-pdf-table', [
            'leader_id' => $this->bupati->id,
            'date' => '2026-10-27',
            'period' => 'tahunan',
        ]))
        ->assertOk()
        ->assertDownload('Tabel_Agenda_Bupati_Test_tahunan_2026-10-27.pdf');
});

test('table pdf export falls back to daily for unknown period', function () {
    $this->actingAs($this->user)
        ->get(route('activities.export-pdf-table', [
            'leader_id' => $this->bupati->id,
            'date' => '2026-10-27',
            'period' => 'tidak-valid',
        ]))
        ->assertOk()
        ->assertDownload('Tabel_Agenda_Bupati_Test_harian_2026-10-27.pdf');
});
