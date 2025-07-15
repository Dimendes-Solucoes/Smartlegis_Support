<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Webhook>
 */
class WebhookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'code' => $this->faker->uuid(),
            'webhook_external_id' => $this->faker->uuid(),
            'webhook_url' => $this->faker->url(),
            'webhook_status' => $this->faker->randomElement(Webhook::STATUSES),
        ];
    }
}
