@php $ev = $registration->slot->position->event; @endphp
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"></head>
<body style="margin:0;background:#f4f4f5;font-family:system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:24px;"><tr><td align="center">
<table width="600" style="max-width:600px;background:#fff;border:1px solid #e4e4e7;border-radius:8px;padding:24px;">
<tr><td style="font-size:18px;font-weight:600;">{{ __('Annulation confirmée') }}</td></tr>
<tr><td style="padding-top:12px;color:#3f3f46;font-size:15px;">
<p>{{ __('Votre inscription à « :title » pour le créneau du :date a été annulée.', ['title' => $ev->title, 'date' => $registration->slot->date?->format('d/m/Y')]) }}</p>
</td></tr>
</table>
</td></tr></table>
</body>
</html>
