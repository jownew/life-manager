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
            ],
            [
                'name' => 'Rent',
            ],
            [
                'name' => 'Internet',
            ],
            [
                'name' => 'Insurance',
            ],
            [
                'name' => 'Telecommunication',
            ],
        ];
    }
}
