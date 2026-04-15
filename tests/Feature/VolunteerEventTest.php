<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Position;
use App\Models\Registration;
use App\Models\Slot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class VolunteerEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_creates_rows_and_redirects(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $event = Event::query()->create([
            'user_id' => $user->id,
            'title' => 'Test',
            'description' => null,
            'slug' => 'test-event',
            'date_start' => now()->toDateString(),
            'date_end' => now()->toDateString(),
            'daily_window_start' => '09:00:00',
            'daily_window_end' => '11:00:00',
            'status' => 'open',
            'public_link_token' => Str::random(40),
        ]);

        $position = Position::query()->create([
            'event_id' => $event->id,
            'name' => 'Accueil',
            'description' => null,
            'color' => '#000000',
            'slot_duration_minutes' => 60,
            'required_per_slot' => 2,
        ]);

        $slot = Slot::query()->create([
            'position_id' => $position->id,
            'date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'max_volunteers' => 2,
        ]);

        $response = $this->post(route('public.evenement.inscription', $event->slug), [
            'firstname' => 'Marie',
            'lastname' => 'Curie',
            'email' => 'marie@example.com',
            'phone' => '+33612345678',
            'slot_ids' => [$slot->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('registrations', [
            'email' => 'marie@example.com',
            'slot_id' => $slot->id,
        ]);
    }

    public function test_cancel_by_token_sets_cancelled_at(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $event = Event::query()->create([
            'user_id' => $user->id,
            'title' => 'Test',
            'description' => null,
            'slug' => 'test-ev',
            'date_start' => now()->toDateString(),
            'date_end' => now()->toDateString(),
            'daily_window_start' => '09:00:00',
            'daily_window_end' => '11:00:00',
            'status' => 'open',
            'public_link_token' => Str::random(40),
        ]);

        $position = Position::query()->create([
            'event_id' => $event->id,
            'name' => 'P',
            'description' => null,
            'color' => '#000000',
            'slot_duration_minutes' => 60,
            'required_per_slot' => 1,
        ]);

        $slot = Slot::query()->create([
            'position_id' => $position->id,
            'date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'max_volunteers' => 1,
        ]);

        $reg = Registration::query()->create([
            'slot_id' => $slot->id,
            'batch_id' => (string) Str::uuid(),
            'firstname' => 'A',
            'lastname' => 'B',
            'email' => 'a@b.com',
            'phone' => null,
            'token' => (string) Str::uuid(),
        ]);

        $this->get(route('evenement_inscription.annuler', $reg->token))->assertOk();
        $reg->refresh();
        $this->assertNotNull($reg->cancelled_at);
    }

    public function test_organizer_cannot_view_other_users_event(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $event = Event::query()->create([
            'user_id' => $owner->id,
            'title' => 'Privé',
            'description' => null,
            'slug' => 'prive',
            'date_start' => now()->toDateString(),
            'date_end' => now()->toDateString(),
            'daily_window_start' => '09:00:00',
            'daily_window_end' => '11:00:00',
            'status' => 'open',
            'registration_enabled' => true,
            'public_link_token' => Str::random(40),
        ]);

        $this->actingAs($other)->get(route('evenements.montrer', $event))->assertForbidden();
    }

    public function test_public_page_visible_when_registration_disabled(): void
    {
        $user = User::factory()->create();
        $event = Event::query()->create([
            'user_id' => $user->id,
            'title' => 'Visible',
            'description' => null,
            'slug' => 'visible-off',
            'date_start' => now()->toDateString(),
            'date_end' => now()->toDateString(),
            'daily_window_start' => '09:00:00',
            'daily_window_end' => '11:00:00',
            'status' => 'open',
            'registration_enabled' => false,
            'public_link_token' => Str::random(40),
        ]);

        $this->get(route('public.evenement', $event->slug))->assertOk();
    }

    public function test_public_registration_rejected_when_registration_disabled(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $event = Event::query()->create([
            'user_id' => $user->id,
            'title' => 'Off',
            'description' => null,
            'slug' => 'reg-off',
            'date_start' => now()->toDateString(),
            'date_end' => now()->toDateString(),
            'daily_window_start' => '09:00:00',
            'daily_window_end' => '11:00:00',
            'status' => 'open',
            'registration_enabled' => false,
            'public_link_token' => Str::random(40),
        ]);

        $position = Position::query()->create([
            'event_id' => $event->id,
            'name' => 'Accueil',
            'description' => null,
            'color' => '#000000',
            'slot_duration_minutes' => 60,
            'required_per_slot' => 2,
        ]);

        $slot = Slot::query()->create([
            'position_id' => $position->id,
            'date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'max_volunteers' => 2,
        ]);

        $this->post(route('public.evenement.inscription', $event->slug), [
            'firstname' => 'Marie',
            'lastname' => 'Curie',
            'email' => 'marie@example.com',
            'phone' => '+33612345678',
            'slot_ids' => [$slot->id],
        ])->assertSessionHasErrors('slot_ids');
    }

    public function test_organizer_can_patch_slot_schedule_and_max(): void
    {
        $user = User::factory()->create();
        $day1 = now()->addWeek()->startOfDay();
        $day2 = $day1->copy()->addDay();
        $event = Event::query()->create([
            'user_id' => $user->id,
            'title' => 'Up',
            'description' => null,
            'slug' => 'slot-patch',
            'date_start' => $day1->toDateString(),
            'date_end' => $day2->toDateString(),
            'daily_window_start' => '08:00:00',
            'daily_window_end' => '20:00:00',
            'status' => 'open',
            'registration_enabled' => true,
            'public_link_token' => Str::random(40),
        ]);
        $position = Position::query()->create([
            'event_id' => $event->id,
            'name' => 'Bar',
            'description' => null,
            'color' => '#9333ea',
            'slot_duration_minutes' => 120,
            'required_per_slot' => 4,
        ]);
        $slot = Slot::query()->create([
            'position_id' => $position->id,
            'date' => $day1->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'max_volunteers' => 4,
        ]);

        $newDate = $day2->toDateString();

        $this->actingAs($user)
            ->patch(route('evenements.creneaux.modifier', [$event->slug, $slot->id]), [
                'date' => $newDate,
                'start_time' => '10:00',
                'end_time' => '12:00',
                'max_volunteers' => 6,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $slot->refresh();
        $this->assertSame($newDate, $slot->date->toDateString());
        $this->assertSame(6, $slot->max_volunteers);
        $this->assertStringStartsWith('10:00', (string) $slot->start_time);
        $this->assertStringStartsWith('12:00', (string) $slot->end_time);
    }
}
