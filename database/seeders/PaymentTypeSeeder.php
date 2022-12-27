<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentType::create(
            [
                'name' => 'Cash',
                'description' => '',
                'user_id' => 1,
            ]
        );    
        PaymentType::create(
            [
                'name' => 'Debit Card',
                'description' => '',
                'user_id' => 1,
            ]
        );    
        PaymentType::create(
            [
                'name' => 'Credit Card',
                'description' => '',
                'user_id' => 1,
            ]
        );    
    }
}
