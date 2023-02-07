<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\PaymentType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $date = fake()->dateTimeBetween('-2 month', '+2 month');
        return [
            'name' => fake()->name(),
            'description' => fake()->name(),
            'amount' => fake()->randomFloat(2),
            'transaction_date' => $date,
            'due_date' =>  $date,
            'paid_date' =>  $date,
            'payment_type_id' => PaymentType::all()->first()->id,
        ];
    }
}
