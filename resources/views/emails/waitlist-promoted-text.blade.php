{{ __('Bonjour :name,', ['name' => $registration->firstname]) }}

{{ __('Une place s’est libérée pour votre créneau. Votre inscription est confirmée.') }}

{{ url('/registration/'.$registration->token.'/cancel') }}
