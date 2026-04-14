{{ __('Confirmation d’inscription') }}

@php $event = $registrations->first()?->slot?->position?->event; @endphp
{{ __('Événement : :title', ['title' => $event->title ?? '']) }}

@foreach ($registrations as $r)
- {{ $r->slot->date?->format('Y-m-d') }} {{ substr((string) $r->slot->start_time,0,5) }} {{ $r->slot->position->name }}
  {{ route('evenement_inscription.annuler', ['token' => $r->token]) }}
@endforeach
