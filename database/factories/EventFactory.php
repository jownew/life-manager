<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = \App\Models\User::all()->random()->first();
        $date = fake()->dateTimeBetween('-2 month', '+2 month')->format('Y-m-d');

        return [
            'user_id' => $user->id,
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'intervals' => 12,
            'reminder' => 60,
            'event_date' => $date,
            'status' => 'active',
            'is_private' => fake()->numberBetween(0, 1),
        ];
    }
}
