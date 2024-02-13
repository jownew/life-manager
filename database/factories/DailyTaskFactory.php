<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyTask>
 */
class DailyTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::pluck('id')->random(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'planned_time' => fake()->dateTime(),
            'completed_time' => fake()->boolean() ? null : fake()->dateTime(),
            'deadline' => fake()->boolean() ? null : fake()->dateTime(),
        ];
    }
}
