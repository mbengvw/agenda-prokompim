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

test('ajax filter request returns only the agenda content partial', function () {
    $this->actingAs($this->user)
        ->get(route('activities.index', ['leader' => 'bupati']), [
            'X-Requested-With' => 'XMLHttpRequest',
        ])
        ->assertOk()
        ->assertSee('data-activity-content', false)
        ->assertSee('data-filter-form', false)
        ->assertSee('filters-changed', false)
        ->assertDontSee('<!DOCTYPE html>', false)
        ->assertDontSee('<html', false);
});

test('non ajax request renders the full agenda page', function () {
    $this->actingAs($this->user)
        ->get(route('activities.index', ['leader' => 'bupati']))
        ->assertOk()
        ->assertSee('id="activity-results"', false)
        ->assertSee('<!DOCTYPE html>', false);
});

test('invalid view parameter falls back to table view', function () {
    $this->actingAs($this->user)
        ->get(route('activities.index', ['leader' => 'bupati', 'view' => 'unknown']))
        ->assertOk()
        ->assertSee('id="view_input" value="table"', false);
});
