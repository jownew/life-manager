<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->getCategories() as $category) {
            Category::create($category);
        }
    }

    private function getCategories()
    {
        return [
            [
                'name' => 'Food',
                'budget' => 500,
            ],
            [
                'name' => 'Rent',
                'budget' => 1500,
            ],
            [
                'name' => 'Internet',
                'budget' => 60
            ],
            [
                'name' => 'Insurance',
                'budget' => 90,
            ],
            [
                'name' => 'Telecommunication',
                'budget' => 120,
            ],
            [
                'name' => 'Transportation',
                'budget' => 110,
            ],
        ];
    }
}
