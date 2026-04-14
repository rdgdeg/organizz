<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportRegistrationsController extends Controller
{
    public function __invoke(Event $event): StreamedResponse
    {
        $this->authorize('manageRegistrations', $event);

        $filename = 'inscriptions-'.$event->slug.'-'.now()->format('Y-m-d').'.csv';

        $query = Registration::query()
            ->with(['slot.position'])
            ->whereHas('slot.position', fn ($q) => $q->where('event_id', $event->id))
            ->whereNull('cancelled_at')
            ->orderBy('id');

        return response()->streamDownload(function () use ($query): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['firstname', 'lastname', 'email', 'phone', 'waitlist', 'checked_in_at', 'position', 'date', 'start_time', 'end_time'], ';');
            $query->chunk(200, function ($regs) use ($out): void {
                foreach ($regs as $r) {
                    fputcsv($out, [
                        $r->firstname,
                        $r->lastname,
                        $r->email,
                        $r->phone,
                        $r->waitlist ? '1' : '0',
                        $r->checked_in_at?->toIso8601String() ?? '',
                        $r->slot->position->name,
                        $r->slot->date?->format('Y-m-d'),
                        (string) $r->slot->start_time,
                        (string) $r->slot->end_time,
                    ], ';');
                }
            });
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
