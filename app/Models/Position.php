<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = [
        'event_id',
        'name',
        'description',
        'color',
        'slot_duration_minutes',
        'required_per_slot',
    ];

    protected function casts(): array
    {
        return [
            'slot_duration_minutes' => 'integer',
            'required_per_slot' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }
}
