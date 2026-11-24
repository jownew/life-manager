<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasUuids;

    protected $fillable = ['user_id', 'name'];

    public function expenses()
    {
        return $this->belongsToMany(Expense::class);
    }
}
