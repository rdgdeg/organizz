<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->withCount(['events as events_count'])
            ->orderByDesc('created_at')
            ->paginate(30)
            ->through(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'plan' => $u->plan,
                'is_super_admin' => $u->isSuperAdmin(),
                'events_count' => $u->events_count,
                'created_at' => $u->created_at?->toIso8601String(),
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'plan' => ['required', 'in:free,pro'],
            'is_super_admin' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'plan' => $validated['plan'],
        ]);

        $this->mergeSuperAdminState($user, (bool) ($validated['is_super_admin'] ?? false));

        return redirect()->route('administration.utilisateurs.index')->with('success', __('Compte créé.'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'plan' => ['required', 'in:free,pro'],
            'is_super_admin' => ['required', 'boolean'],
        ]);

        $wantsSuper = $request->boolean('is_super_admin');

        if ($user->id === $request->user()->id && ! $wantsSuper) {
            return redirect()->back()->with('message', __('Vous ne pouvez pas retirer votre propre statut super admin.'));
        }

        $user->plan = $validated['plan'];
        $user->save();

        $this->mergeSuperAdminState($user, $wantsSuper);

        return redirect()->back()->with('success', __('Utilisateur mis à jour.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->back()->with('message', __('Supprimez votre compte depuis la page profil.'));
        }

        $user->delete();

        return redirect()->route('administration.utilisateurs.index')->with('success', __('Compte supprimé.'));
    }

    private function mergeSuperAdminState(User $user, bool $fromForm): void
    {
        $emails = config('organizz.super_admin_emails', []);
        $byEmail = $emails !== [] && in_array(strtolower((string) $user->email), $emails, true);
        $user->forceFill([
            'is_super_admin' => $fromForm || $byEmail,
        ])->save();
    }
}
