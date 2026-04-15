<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    /** URLs organisateur : /evenements/{slug} (accepte aussi l’ID pour les anciens liens). */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (is_numeric($value)) {
            return static::query()->whereKey((int) $value)->firstOrFail();
        }

        return static::query()->where('slug', $value)->firstOrFail();
    }

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'additional_info',
        'recommendations',
        'practical_details',
        'slug',
        'date_start',
        'date_end',
        'daily_window_start',
        'daily_window_end',
        'status',
        'registration_enabled',
        'public_link_token',
        'notify_organizer_on_registration',
        'custom_fields',
        'waitlist_enabled',
        'participant_edit_deadline_hours',
        'matching_enabled',
        'embed_token',
    ];

    protected function casts(): array
    {
        return [
            'date_start' => 'date',
            'date_end' => 'date',
            'notify_organizer_on_registration' => 'boolean',
            'custom_fields' => 'array',
            'waitlist_enabled' => 'boolean',
            'participant_edit_deadline_hours' => 'integer',
            'matching_enabled' => 'boolean',
            'registration_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function reminderRules(): HasMany
    {
        return $this->hasMany(ReminderRule::class);
    }

    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')->withPivot('role')->withTimestamps();
    }

    public function participantEditDeadline(): Carbon
    {
        return $this->date_start->copy()->startOfDay()->subHours($this->participant_edit_deadline_hours ?? 48);
    }

    public function canParticipantEditNow(): bool
    {
        return now()->lt($this->participantEditDeadline());
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /** Événement actif (rappels, logique « en cours »). */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Page publique / iframe : visible (pas brouillon ni archivé).
     */
    public function isPubliclyVisible(): bool
    {
        return in_array($this->status, ['open', 'closed'], true);
    }

    /**
     * Formulaire d’inscription : ouvert si le gestionnaire l’a laissé actif et que l’événement est « ouvert ».
     */
    public function acceptsRegistrations(): bool
    {
        return $this->registration_enabled && $this->status === 'open';
    }
}
