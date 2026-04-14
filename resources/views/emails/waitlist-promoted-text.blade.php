{{ __('Bonjour :name,', ['name' => $registration->firstname]) }}

{{ __('Une place s’est libérée pour votre créneau. Votre inscription est confirmée.') }}

{{ route('evenement_inscription.annuler', ['token' => $registration->token]) }}
