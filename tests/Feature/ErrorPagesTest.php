<?php

it('shows custom 404 page with styled layout', function () {
    $this->get('/this-route-does-not-exist')
        ->assertNotFound()
        ->assertSee('Page not found', false);
});
