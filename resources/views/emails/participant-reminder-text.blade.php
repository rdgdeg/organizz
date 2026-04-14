{{ $manual ? __('Votre planning') : __('Rappel — :days jours', ['days' => $daysBefore]) }}
{{ $event->title }}

@foreach ($registrations as $r)
- {{ $r->slot->date?->format('Y-m-d') }} {{ $r->slot->position->name }} {{ url('/registration/'.$r->token.'/cancel') }}
@endforeach
