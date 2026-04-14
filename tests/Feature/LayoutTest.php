<?php

it('renders the shared layout on the welcome route', function () {
    $response = $this->get('/');
    $response->assertOk();
    $response->assertSee('Portfolio', false);
    $response->assertSee('Home', false);
    $response->assertSee('Contact', false);
});
