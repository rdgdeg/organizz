<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Event $event): bool
    {
        return $this->role($user, $event) !== null;
    }

    /** Propriétaire ou co-organisateur « admin » : paramètres, postes, créneaux, rappels, duplication. */
    public function configure(User $user, Event $event): bool
    {
        $r = $this->role($user, $event);

        return $r === 'owner' || $r === 'admin';
    }

    /** Alias historique : édition événement = configure. */
    public function update(User $user, Event $event): bool
    {
        return $this->configure($user, $event);
    }

    /** Inscriptions, export, pointage, récap — pas la structure de l’événement. */
    public function manageRegistrations(User $user, Event $event): bool
    {
        $r = $this->role($user, $event);

        return in_array($r, ['owner', 'admin', 'registrations'], true);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->role($user, $event) === 'owner';
    }

    public function manageCollaborators(User $user, Event $event): bool
    {
        return $this->role($user, $event) === 'owner';
    }

    private function role(User $user, Event $event): ?string
    {
        if ($user->id === $event->user_id) {
            return 'owner';
        }

        $pivot = $event->collaborators()->where('users.id', $user->id)->first();

        return $pivot?->pivot->role;
    }
}
