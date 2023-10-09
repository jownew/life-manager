<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'intervals',
        'reminder',
        'event_date',
        'action_date',
        'status',
        'is_private',
        'is_owed',
        'currency_id',
        'amount',
        'user_id',
    ];

    protected $appends = ['days'];

    protected $casts = [
        'days' => 'integer',
    ];

    protected function days(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => $this->getDays($attributes['event_date']),
        );
    }

    private function getDays($eventDate)
    {
        if (empty($eventDate)) {
            return 0;
        }

        $datetime1 = new \DateTime();
        $datetime2 = new \DateTime($eventDate);
        $interval = $datetime1->diff($datetime2);
        $days = $interval->format('%a');

        if ($interval->invert) {
            return $days * -1;
        }

        return $days;
    }

    public function scopeFilter($query, array $filters)
    {
        $query
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    return $query
                        ->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when(!isset($filters['status']), function($query, $status) {
                $query->where('status', 'active');
            })
            ->when($filters['trashed'] ?? null, function ($query, $trashed) {
                if ($trashed === 'with') {
                    $query->withTrashed();
                } elseif ($trashed === 'only') {
                    $query->onlyTrashed();
                }
            });
    }
}
