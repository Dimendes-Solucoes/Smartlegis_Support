<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserListTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_a_list_of_users()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        User::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'users' => [
                    '*' => ['id', 'name', 'email', 'created_at', 'updated_at']
                ]
            ])
            ->assertJsonCount(4, 'users'); // 4 users: 1 admin + 3 created by factory
    }
}
