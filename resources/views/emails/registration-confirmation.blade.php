@php
    $event = $registrations->first()?->slot?->position?->event;
    $organizerEmail = $event?->user?->email;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width"></head>
<body style="margin:0;background:#f4f4f5;font-family:system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:24px;"><tr><td align="center">
<table width="600" style="max-width:600px;background:#fff;border:1px solid #e4e4e7;border-radius:8px;padding:24px;">
<tr><td style="font-size:18px;font-weight:600;color:#18181b;">{{ __('Confirmation d’inscription') }}</td></tr>
<tr><td style="padding-top:12px;color:#3f3f46;font-size:15px;line-height:1.5;">
<p>{{ __('Bonjour :name,', ['name' => $registrations->first()->firstname]) }}</p>
<p>{{ __('Votre inscription à l’événement « :title » est bien enregistrée.', ['title' => $event->title ?? '']) }}</p>
<table cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin-top:12px;font-size:14px;">
<tr style="background:#fafafa;"><th align="left">{{ __('Date') }}</th><th align="left">{{ __('Créneau') }}</th><th align="left">{{ __('Poste') }}</th></tr>
@foreach ($registrations as $r)
<tr style="border-top:1px solid #e4e4e7;">
<td>{{ $r->slot->date?->format('d/m/Y') }}</td>
<td>{{ substr((string) $r->slot->start_time,0,5) }} – {{ substr((string) $r->slot->end_time,0,5) }}</td>
<td>{{ $r->slot->position->name }}</td>
</tr>
@endforeach
</table>
@php
    $portal = \App\Models\ParticipantEmailToken::query()->where('email', strtolower($registrations->first()->email))->first();
@endphp
@if ($portal)
<p style="margin-top:16px;font-size:14px;"><a href="{{ route('participant.espace', ['token' => $portal->token]) }}" style="color:#2563eb;font-weight:600;">{{ __('Mes inscriptions — tout voir et gérer sur une seule page') }}</a></p>
@endif
<p style="margin-top:16px;font-size:14px;">{{ __('Pour annuler un créneau directement :') }}</p>
@foreach ($registrations as $r)
<p style="font-size:14px;"><a href="{{ route('evenement_inscription.annuler', ['token' => $r->token]) }}" style="color:#2563eb;">{{ __('Annuler ce créneau') }}</a></p>
@endforeach
@if ($organizerEmail)
<p style="margin-top:16px;font-size:14px;">{{ __('Contact organisateur : :email', ['email' => $organizerEmail]) }}</p>
@endif
</td></tr>
</table>
</td></tr></table>
</body>
</html>
