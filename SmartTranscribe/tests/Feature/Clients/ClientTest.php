<?php

namespace Tests\Feature\Clients;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_all_clients()
    {
        $user = User::factory()->create();

        $clients = Client::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/admin/clients');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'clients')
            ->assertJsonFragment([
                'id' => $clients[0]->id,
                'code' => $clients[0]->code,
                'website_name' => $clients[0]->website_name,
                'webhook_url' => $clients[0]->webhook_url,
                'token' => $clients[0]->token
            ])
            ->assertJsonStructure([
                'clients' => [
                    '*' => ['id', 'code', 'website_name', 'webhook_url', 'token']
                ]
            ]);
    }

    public function test_it_returns_empty_array_when_no_clients_exist()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/admin/clients');

        $response->assertStatus(200)
            ->assertJson([
                'clients' => []
            ]);
    }

    public function test_deleted_clients_are_not_returned()
    {
        $user = User::factory()->create();

        Client::factory()->count(2)->create();
        $client = Client::factory()->create();

        $client->delete();

        $response = $this->actingAs($user)
            ->getJson('/api/admin/clients');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'clients')
            ->assertJsonMissing([
                'id' => $client->id,
                'code' => $client->code,
            ]);
    }
}
