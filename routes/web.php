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

Route::get('/', LandingController::class)->name('landing');

Route::get('/event/{slug}', [EventPublicController::class, 'show'])->name('public.event');
Route::post('/event/{slug}/register', [EventPublicController::class, 'register'])
    ->middleware('throttle:10,1')
    ->name('public.event.register');
Route::get('/event/{slug}/confirmation/{batch}', [EventPublicController::class, 'confirm'])->name('public.event.confirm');
Route::get('/event/{slug}/qr.png', EventQrController::class)->name('public.event.qr');

Route::get('/embed/{embed_token}', EmbedEventController::class)->name('public.event.embed');

Route::get('/participant/{token}', [ParticipantPortalController::class, 'show'])->name('participant.portal');
Route::post('/participant/{token}/cancel', [ParticipantPortalController::class, 'cancel'])->name('participant.portal.cancel');

Route::get('/registration/{token}/cancel', [RegistrationTokenController::class, 'cancel'])->name('registration.cancel');
Route::get('/registration/{token}/confirm', [RegistrationTokenController::class, 'confirm'])->name('registration.confirm');

Route::get('/dashboard', [EventController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/upgrade', UpgradeController::class)
    ->middleware(['auth', 'verified'])
    ->name('upgrade');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])
        ->middleware('plan.limit:create_event')
        ->name('events.store');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::patch('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::post('/events/{event}/duplicate', EventDuplicateController::class)
        ->middleware('plan.limit:create_event')
        ->name('events.duplicate');

    Route::get('/events/{event}/collaborators', [EventCollaboratorController::class, 'index'])->name('events.collaborators.index');
    Route::post('/events/{event}/collaborators', [EventCollaboratorController::class, 'store'])->name('events.collaborators.store');
    Route::delete('/events/{event}/collaborators/{user}', [EventCollaboratorController::class, 'destroy'])->name('events.collaborators.destroy');

    Route::get('/events/{event}/export', ExportRegistrationsController::class)
        ->middleware('plan.limit:export_csv')
        ->name('events.export');

    Route::get('/events/{event}/positions', [PositionController::class, 'index'])->name('events.positions.index');
    Route::post('/events/{event}/positions', [PositionController::class, 'store'])
        ->middleware('plan.limit:create_position')
        ->name('events.positions.store');
    Route::patch('/events/{event}/positions/{position}', [PositionController::class, 'update'])->name('events.positions.update');
    Route::delete('/events/{event}/positions/{position}', [PositionController::class, 'destroy'])->name('events.positions.destroy');
    Route::post('/events/{event}/positions/{position}/regenerate', [PositionController::class, 'regenerate'])->name('events.positions.regenerate');

    Route::post('/events/{event}/slots', [SlotController::class, 'store'])->name('events.slots.store');
    Route::delete('/events/{event}/slots/{slot}', [SlotController::class, 'destroy'])->name('events.slots.destroy');

    Route::get('/events/{event}/registrations', [RegistrationAdminController::class, 'index'])->name('events.registrations.index');
    Route::delete('/events/{event}/registrations/{registration}', [RegistrationAdminController::class, 'destroy'])->name('events.registrations.destroy');
    Route::post('/events/{event}/registrations/{registration}/recap', [RegistrationAdminController::class, 'sendRecap'])->name('events.registrations.recap');
    Route::post('/events/{event}/registrations/{registration}/check-in', RegistrationCheckInController::class)->name('events.registrations.checkin');

    Route::get('/events/{event}/reminders', [ReminderRuleController::class, 'index'])->name('events.reminders.index');
    Route::post('/events/{event}/reminders', [ReminderRuleController::class, 'store'])
        ->middleware('plan.limit:create_reminder')
        ->name('events.reminders.store');
    Route::patch('/events/{event}/reminders/{reminderRule}', [ReminderRuleController::class, 'update'])->name('events.reminders.update');
    Route::delete('/events/{event}/reminders/{reminderRule}', [ReminderRuleController::class, 'destroy'])->name('events.reminders.destroy');
    Route::post('/events/{event}/reminders/test', [ReminderRuleController::class, 'testEmail'])->name('events.reminders.test');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
