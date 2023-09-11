<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Currency;
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
        $date = fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d');
        $user = \App\Models\User::all()->random()->first();

        return [
            'user_id' => $user->id,
            'name' => fake()->words(fake()->numberBetween(1, 3), true),
            'description' => fake()->paragraph(fake()->numberBetween(1, 2), false),
            'category_id' => Category::all()->random()->id,
            'amount' => fake()->randomFloat(2, 1, 500),
            'transaction_date' => $date,
            'due_date' =>  $date,
            'paid_date' =>  $date,
            'payment_type_id' => PaymentType::pluck('id')->random(),
            'currency_id' => Currency::pluck('id')->random(),
        ];
    }
}
