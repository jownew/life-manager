<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Currency extends Model
{
    use HasUUids;
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
