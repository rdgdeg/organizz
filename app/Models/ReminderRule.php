<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderRule extends Model
{
    protected $fillable = [
        'event_id',
        'days_before',
        'time_of_day',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'days_before' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
