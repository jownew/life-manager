<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Category extends Model
{
    use HasUUids;
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
    ];

    public function expenses()
    {
        return $this->belongsToMany(Expense::class);
    }
    
    public static function addCategory($expenseId, $categoryName)
    {
        if (!$categoryName) {
            return;
        }
        
        $category = Category::where('name', 'like', $categoryName)->first();

        if (!$category) {
            $category = Category::create(['name' => $categoryName]);
        }

        $category->expenses()->attach($expenseId);
    }
}
