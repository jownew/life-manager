<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Currency extends Model
{
    use HasUUids;
    use HasFactory;

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
