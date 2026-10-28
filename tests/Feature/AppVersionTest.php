<?php

test('version endpoint exposes the current version for installed clients', function () {
    config([
        'app.version' => '2.1.0',
        'app.min_client_version' => '2.0.0',
    ]);

    $response = $this->get('/version.json');

    $response
        ->assertOk()
        ->assertJson([
            'version' => '2.1.0',
            'min_version' => '2.0.0',
        ])
        ->assertJsonStructure(['version', 'min_version', 'build']);

    expect($response->headers->get('Cache-Control'))->toContain('no-store');
});

test('pages expose the version metadata used by the update guard', function () {
    config(['app.version' => '2.1.0']);

    $this->get('/')
        ->assertOk()
        ->assertSee('<meta name="app-version" content="2.1.0">', false)
        ->assertSee('app-build', false)
        ->assertSee('pwa-update-guard', false);
});
