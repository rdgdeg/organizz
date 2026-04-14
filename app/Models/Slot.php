<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slot extends Model
{
    protected $fillable = [
        'position_id',
        'date',
        'start_time',
        'end_time',
        'max_volunteers',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'max_volunteers' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /** Inscriptions confirmées (hors liste d’attente). */
    public function activeRegistrations(): HasMany
    {
        return $this->registrations()
            ->whereNull('cancelled_at')
            ->where('waitlist', false);
    }

    public function takenCount(): int
    {
        return $this->activeRegistrations()->count();
    }

    public function spotsRemaining(): int
    {
        return max(0, $this->max_volunteers - $this->takenCount());
    }

    public function isFull(): bool
    {
        return $this->spotsRemaining() === 0;
    }
}
