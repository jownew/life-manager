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
        $date = fake()->dateTimeBetween('-2 month', '+2 month')->format('Y-m-d');
        return [
            'name' => fake()->name(),
            'description' => fake()->name(),
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
