<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"></head>
<body style="margin:0;background:#f4f4f5;font-family:system-ui,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:24px;"><tr><td align="center">
<table width="600" style="max-width:600px;background:#fff;border:1px solid #e4e4e7;border-radius:8px;padding:24px;">
<tr><td style="font-size:18px;font-weight:600;">{{ __('Nouvelle inscription') }}</td></tr>
<tr><td style="padding-top:12px;color:#3f3f46;font-size:15px;">
<p>{{ __('Événement : :title', ['title' => $event->title]) }}</p>
<ul>
@foreach ($registrations as $r)
<li>{{ $r->firstname }} {{ $r->lastname }} — {{ $r->email }} — {{ $r->slot->position->name }} ({{ $r->slot->date?->format('d/m/Y') }})</li>
@endforeach
</ul>
</td></tr>
</table>
</td></tr></table>
</body>
</html>
