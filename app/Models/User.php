<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'plan'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (User $user): void {
            $list = config('organizz.super_admin_emails', []);
            if ($list !== [] && in_array(strtolower((string) $user->email), $list, true) && ! $user->is_super_admin) {
                $user->forceFill(['is_super_admin' => true])->saveQuietly();
            }
        });
    }

    public function isSuperAdmin(): bool
    {
        if ((bool) ($this->is_super_admin ?? false)) {
            return true;
        }

        $list = config('organizz.super_admin_emails', []);

        return $list !== [] && in_array(strtolower((string) $this->email), $list, true);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function collaboratedEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_user')->withPivot('role')->withTimestamps();
    }

    public function eventRole(Event $event): ?string
    {
        if ($this->id === $event->user_id) {
            return 'owner';
        }

        return $this->collaboratedEvents()->where('events.id', $event->id)->first()?->pivot->role;
    }

    public function isPro(): bool
    {
        return $this->plan === 'pro';
    }
}
