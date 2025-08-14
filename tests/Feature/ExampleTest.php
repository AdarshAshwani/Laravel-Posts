<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->followingRedirects()->get('/'); // follow 302 redirect
        $response->assertOk(); // expect final response to be 200 OK
    }
}
