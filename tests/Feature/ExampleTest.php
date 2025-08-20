<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_is_ok_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/')->assertOk();
    }
}
