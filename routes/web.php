<?php

use App\Http\Controllers\Organizer\EventCollaboratorController;
use App\Http\Controllers\Organizer\EventController;
use App\Http\Controllers\Organizer\EventDuplicateController;
use App\Http\Controllers\Organizer\ExportRegistrationsController;
use App\Http\Controllers\Organizer\PositionController;
use App\Http\Controllers\Organizer\RegistrationAdminController;
use App\Http\Controllers\Organizer\RegistrationCheckInController;
use App\Http\Controllers\Organizer\ReminderRuleController;
use App\Http\Controllers\Organizer\SlotController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\EmbedEventController;
use App\Http\Controllers\Public\EventPublicController;
use App\Http\Controllers\Public\EventQrController;
use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\Public\ParticipantPortalController;
use App\Http\Controllers\Public\RegistrationTokenController;
use App\Http\Controllers\UpgradeController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('accueil');

Route::get('/evenement/{slug}', [EventPublicController::class, 'show'])->name('public.evenement');
Route::post('/evenement/{slug}/inscription', [EventPublicController::class, 'register'])
    ->middleware('throttle:10,1')
    ->name('public.evenement.inscription');
Route::get('/evenement/{slug}/confirmation/{batch}', [EventPublicController::class, 'confirm'])->name('public.evenement.confirmation');
Route::get('/evenement/{slug}/qr.png', EventQrController::class)->name('public.evenement.qr');

Route::get('/integration/{embed_token}', EmbedEventController::class)->name('integration.evenement');

Route::get('/participant/{token}', [ParticipantPortalController::class, 'show'])->name('participant.espace');
Route::post('/participant/{token}/annuler', [ParticipantPortalController::class, 'cancel'])->name('participant.annuler');

Route::get('/inscription-evenement/{token}/annuler', [RegistrationTokenController::class, 'cancel'])->name('evenement_inscription.annuler');
Route::get('/inscription-evenement/{token}/confirmer', [RegistrationTokenController::class, 'confirm'])->name('evenement_inscription.confirmer');

Route::get('/tableau-de-bord', [EventController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/abonnement', UpgradeController::class)
    ->middleware(['auth', 'verified'])
    ->name('upgrade');

Route::middleware(['auth', 'verified'])->group(function () {
    /** Liste des événements (identique au tableau de bord — évite le 405 sur GET /evenements). */
    Route::get('/evenements', [EventController::class, 'index'])->name('evenements.index');
    Route::get('/evenements/creer', [EventController::class, 'create'])->name('evenements.creer');
    Route::post('/evenements', [EventController::class, 'store'])
        ->middleware('plan.limit:create_event')
        ->name('evenements.enregistrer');
    Route::get('/evenements/{event}', [EventController::class, 'show'])->name('evenements.montrer');
    Route::get('/evenements/{event}/editer', [EventController::class, 'edit'])->name('evenements.editer');
    Route::patch('/evenements/{event}', [EventController::class, 'update'])->name('evenements.mettre_a_jour');
    Route::patch('/evenements/{event}/inscriptions-publiques', [EventController::class, 'updateRegistrationEnabled'])
        ->name('evenements.inscriptions_publiques');
    Route::delete('/evenements/{event}', [EventController::class, 'destroy'])->name('evenements.supprimer');
    Route::post('/evenements/{event}/dupliquer', EventDuplicateController::class)
        ->middleware('plan.limit:create_event')
        ->name('evenements.dupliquer');

    Route::get('/evenements/{event}/coorganisateurs', [EventCollaboratorController::class, 'index'])->name('evenements.coorganisateurs.index');
    Route::post('/evenements/{event}/coorganisateurs', [EventCollaboratorController::class, 'store'])->name('evenements.coorganisateurs.ajouter');
    Route::delete('/evenements/{event}/coorganisateurs/{user}', [EventCollaboratorController::class, 'destroy'])->name('evenements.coorganisateurs.retirer');

    Route::get('/evenements/{event}/export', ExportRegistrationsController::class)
        ->middleware('plan.limit:export_csv')
        ->name('evenements.export');

    Route::get('/evenements/{event}/postes', [PositionController::class, 'index'])->name('evenements.postes.index');
    Route::post('/evenements/{event}/postes', [PositionController::class, 'store'])
        ->middleware('plan.limit:create_position')
        ->name('evenements.postes.enregistrer');
    Route::patch('/evenements/{event}/postes/{position}', [PositionController::class, 'update'])->name('evenements.postes.modifier');
    Route::delete('/evenements/{event}/postes/{position}', [PositionController::class, 'destroy'])->name('evenements.postes.supprimer');
    Route::post('/evenements/{event}/postes/{position}/regenerer', [PositionController::class, 'regenerate'])->name('evenements.postes.regenerer');

    Route::post('/evenements/{event}/creneaux', [SlotController::class, 'store'])->name('evenements.creneaux.enregistrer');
    Route::patch('/evenements/{event}/creneaux/{slot}', [SlotController::class, 'update'])->name('evenements.creneaux.modifier');
    Route::delete('/evenements/{event}/creneaux/{slot}', [SlotController::class, 'destroy'])->name('evenements.creneaux.supprimer');

    Route::get('/evenements/{event}/inscriptions', [RegistrationAdminController::class, 'index'])->name('evenements.inscriptions.index');
    Route::delete('/evenements/{event}/inscriptions/{registration}', [RegistrationAdminController::class, 'destroy'])->name('evenements.inscriptions.supprimer');
    Route::post('/evenements/{event}/inscriptions/{registration}/recap', [RegistrationAdminController::class, 'sendRecap'])->name('evenements.inscriptions.recap');
    Route::post('/evenements/{event}/inscriptions/{registration}/presence', RegistrationCheckInController::class)->name('evenements.inscriptions.presence');

    Route::get('/evenements/{event}/rappels', [ReminderRuleController::class, 'index'])->name('evenements.rappels.index');
    Route::post('/evenements/{event}/rappels', [ReminderRuleController::class, 'store'])
        ->middleware('plan.limit:create_reminder')
        ->name('evenements.rappels.enregistrer');
    Route::patch('/evenements/{event}/rappels/{reminderRule}', [ReminderRuleController::class, 'update'])->name('evenements.rappels.modifier');
    Route::delete('/evenements/{event}/rappels/{reminderRule}', [ReminderRuleController::class, 'destroy'])->name('evenements.rappels.supprimer');
    Route::post('/evenements/{event}/rappels/test', [ReminderRuleController::class, 'testEmail'])->name('evenements.rappels.test');
});

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Anciennes URLs (redirection 301)
|--------------------------------------------------------------------------
*/
Route::permanentRedirect('/dashboard', '/tableau-de-bord');
Route::permanentRedirect('/upgrade', '/abonnement');
Route::get('/login', fn () => redirect('/connexion', 301));
Route::get('/register', fn () => redirect('/inscription', 301));
Route::get('/profile', fn () => redirect('/profil', 301));
Route::get('/event/{slug}', fn (string $slug) => redirect()->route('public.evenement', $slug, 301))->where('slug', '[^/]+');
Route::get('/embed/{embed_token}', fn (string $embed_token) => redirect()->route('integration.evenement', $embed_token, 301));
Route::get('/events/create', fn () => redirect('/evenements/creer', 301));
Route::get('/events/{path}', fn (string $path) => redirect('/evenements/'.$path, 301))->where('path', '.*');

require __DIR__.'/auth.php';
