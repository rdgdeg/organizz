@extends('layouts.embed')

@section('title', $event->title)

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6">
        <p class="mb-4 text-center text-xs text-slate-500">{{ __('Formulaire intégré — ') }} <a href="{{ route('public.evenement', $event->slug) }}" class="font-medium text-brand-700 underline" target="_blank" rel="noopener">{{ __('Ouvrir la page complète') }}</a></p>
        @include('public.event-content')
    </div>
@endsection
