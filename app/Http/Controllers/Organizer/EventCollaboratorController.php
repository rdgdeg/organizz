<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class EventCollaboratorController extends Controller
{
    public function index(Event $event): Response
    {
        $this->authorize('manageCollaborators', $event);

        $rows = $event->collaborators()->orderBy('name')->get()->map(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => $u->pivot->role,
        ]);

        return Inertia::render('Organizer/Events/Collaborators', [
            'event' => [
                'id' => $event->id,
                'slug' => $event->slug,
                'title' => $event->title,
            ],
            'collaborators' => $rows,
            'roleOptions' => [
                ['value' => 'admin', 'label' => __('Admin complet')],
                ['value' => 'registrations', 'label' => __('Gestion des inscriptions')],
                ['value' => 'viewer', 'label' => __('Lecture seule')],
            ],
        ]);
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manageCollaborators', $event);

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', Rule::in(['admin', 'registrations', 'viewer'])],
        ]);

        $user = User::query()->where('email', strtolower($validated['email']))->first();
        if (! $user) {
            return back()->withErrors(['email' => __('Aucun compte avec cet email. L’utilisateur doit d’abord s’inscrire.')]);
        }

        if ($user->id === $event->user_id) {
            return back()->withErrors(['email' => __('Le propriétaire est déjà associé à l’événement.')]);
        }

        $event->collaborators()->syncWithoutDetaching([
            $user->id => ['role' => $validated['role']],
        ]);

        return back()->with('success', __('Co-organisateur ajouté.'));
    }

    public function destroy(Event $event, User $user): RedirectResponse
    {
        $this->authorize('manageCollaborators', $event);

        $event->collaborators()->detach($user->id);

        return back()->with('success', __('Accès retiré.'));
    }
}
