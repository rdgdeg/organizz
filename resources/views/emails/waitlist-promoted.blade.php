@php
    $event = $registration->slot?->position?->event;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width"></head>
<body style="margin:0;background:#f4f4f5;font-family:system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:24px;"><tr><td align="center">
<table width="600" style="max-width:600px;background:#fff;border:1px solid #e4e4e7;border-radius:8px;padding:24px;">
<tr><td style="font-size:18px;font-weight:600;color:#18181b;">{{ __('Place disponible') }}</td></tr>
<tr><td style="padding-top:12px;color:#3f3f46;font-size:15px;line-height:1.5;">
<p>{{ __('Bonjour :name,', ['name' => $registration->firstname]) }}</p>
<p>{{ __('Une place vient de se libérer pour le créneau suivant à l’événement « :title ». Votre inscription est maintenant confirmée.', ['title' => $event->title ?? '']) }}</p>
<p><strong>{{ $registration->slot->date?->format('d/m/Y') }}</strong> — {{ substr((string) $registration->slot->start_time,0,5) }} – {{ substr((string) $registration->slot->end_time,0,5) }} — {{ $registration->slot->position->name }}</p>
<p style="margin-top:16px;font-size:14px;"><a href="{{ url('/registration/'.$registration->token.'/cancel') }}" style="color:#2563eb;">{{ __('Annuler ce créneau') }}</a></p>
</td></tr>
</table>
</td></tr></table>
</body>
</html>
