<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/admin/auth/profile');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_profile()
    {
        $response = $this->getJson('/api/admin/auth/profile');
        $response->assertStatus(401);
    }
}
