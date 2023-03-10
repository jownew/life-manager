<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PaymentType extends Model
{
    use HasUuids;
    use HasFactory;

    protected $fillable = [
        'name',
    ];    

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
