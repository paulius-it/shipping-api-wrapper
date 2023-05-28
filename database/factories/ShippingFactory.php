<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipping>
 */
class ShippingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tracking_number' => 'random_shipping_tracking',
            'provider_id' => 1,
            'user_id' => 5,
            'sender_address' => 'siuntejo adresas',
            'receiver_address' => 'gavejo adresas',
            'phone' => '+37000000000',
            'receiver_email' => 'info@gmail.com',
            'item' => '[[Shipping item]]',
            'quantity' => 10,
            'status' => 'sent',
        ];
    }
}
