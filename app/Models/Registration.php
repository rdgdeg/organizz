<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    protected $fillable = [
        'slot_id',
        'batch_id',
        'firstname',
        'lastname',
        'email',
        'phone',
        'custom_field_answers',
        'waitlist',
        'waitlist_position',
        'token',
        'confirmed_at',
        'cancelled_at',
        'reminder_sent_at',
        'checked_in_at',
        'preferred_reminder_channel',
    ];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'custom_field_answers' => 'array',
            'waitlist' => 'boolean',
            'waitlist_position' => 'integer',
        ];
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    public function isWaitlist(): bool
    {
        return $this->waitlist === true;
    }

    public function isConfirmedBooking(): bool
    {
        return ! $this->isCancelled() && ! $this->isWaitlist();
    }

    public function scopeConfirmedBooking($query)
    {
        return $query->whereNull('cancelled_at')->where('waitlist', false);
    }
}
