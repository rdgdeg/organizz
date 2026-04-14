<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Registration;
use App\Models\User;
use App\Services\SlotGeneratorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->create([
            'name' => 'Organisateur démo',
            'email' => 'organizer@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'plan' => 'free',
        ]);

        $event = $user->events()->create([
            'title' => 'Week-end solidaire (démo)',
            'description' => "Événement multi-jours pour tester les créneaux bénévoles.\nCompte démo : organizer@example.com / password",
            'slug' => 'week-end-solidaire-demo',
            'date_start' => now()->addDays(10)->startOfDay()->toDateString(),
            'date_end' => now()->addDays(12)->startOfDay()->toDateString(),
            'daily_window_start' => '08:00:00',
            'daily_window_end' => '14:00:00',
            'status' => 'open',
            'public_link_token' => Str::random(48),
            'notify_organizer_on_registration' => false,
        ]);

        $positions = [
            ['name' => 'Accueil', 'color' => '#6366f1', 'slot_duration_minutes' => 120, 'required_per_slot' => 2],
            ['name' => 'Bar', 'color' => '#22c55e', 'slot_duration_minutes' => 120, 'required_per_slot' => 2],
            ['name' => 'Parking', 'color' => '#f97316', 'slot_duration_minutes' => 120, 'required_per_slot' => 1],
        ];

        $slotGenerator = app(SlotGeneratorService::class);

        foreach ($positions as $data) {
            /** @var Position $position */
            $position = $event->positions()->create([
                'name' => $data['name'],
                'description' => 'Poste de démonstration',
                'color' => $data['color'],
                'slot_duration_minutes' => $data['slot_duration_minutes'],
                'required_per_slot' => $data['required_per_slot'],
            ]);
            $slotGenerator->regenerate($position);
        }

        $firstSlots = $event->positions()->with('slots')->get()->pluck('slots')->flatten()->take(4);

        foreach ($firstSlots as $i => $slot) {
            Registration::query()->create([
                'slot_id' => $slot->id,
                'batch_id' => (string) Str::uuid(),
                'firstname' => 'Jean',
                'lastname' => 'Test'.($i + 1),
                'email' => 'benevole'.($i + 1).'@example.com',
                'phone' => '+32470123456',
                'token' => (string) Str::uuid(),
            ]);
        }

        $cancelSlot = $firstSlots->last();
        if ($cancelSlot) {
            Registration::query()->create([
                'slot_id' => $cancelSlot->id,
                'batch_id' => (string) Str::uuid(),
                'firstname' => 'Annulé',
                'lastname' => 'Démo',
                'email' => 'annule@example.com',
                'phone' => null,
                'token' => (string) Str::uuid(),
                'cancelled_at' => now(),
            ]);
        }

        $event->reminderRules()->create([
            'days_before' => 3,
            'time_of_day' => '09:00',
            'active' => true,
        ]);
    }
}
