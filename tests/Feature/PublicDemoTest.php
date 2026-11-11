<?php

test('the normal instance retains login and disables the demo entry point', function () {
    config(['demo.enabled' => false]);

    $this->get('/login')->assertOk()->assertSee('Explorar demo sin credenciales');
    $this->post('/demo')->assertNotFound();
});

test('demo mode refuses to run on the normal database', function () {
    config(['demo.enabled' => true]);

    $this->get('/login')->assertServiceUnavailable();
});

test('demo mode rejects writes and routes outside the tour', function () {
    $originalDatabase = config('database.default');
    config(['demo.enabled' => true, 'database.default' => 'public_demo']);

    try {
        $this->get('/login')->assertOk()->assertSee('Explorar demo');
        $this->get('/clientes')->assertRedirect('/login');
        $this->actingAs(new \App\Models\User(['role' => 'admin']));
        $this->get('/profile')->assertForbidden();
        $this->get('/register')->assertForbidden();
        $this->post('/login')->assertForbidden();
        $this->post('/clientes/1/assign')->assertForbidden();
        $this->patch('/mis-clientes/1/estado')->assertForbidden();
        $this->post('/mis-clientes/1/finalizar')->assertForbidden();
    } finally {
        config(['database.default' => $originalDatabase]);
    }
});
