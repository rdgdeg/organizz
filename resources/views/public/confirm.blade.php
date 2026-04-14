@extends('layouts.public')

@section('title', __('Confirmation'))

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-12">
    <div class="public-card text-center sm:text-left">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 ring-4 ring-emerald-50">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="mt-6 text-2xl font-bold tracking-tight text-slate-900">{{ __('Merci !') }}</h1>
        <p class="mt-2 text-slate-600">{{ __('Votre inscription à « :title » est enregistrée.', ['title' => $event->title]) }}</p>

        <ul class="mt-8 space-y-3 text-left text-sm">
            @foreach ($registrations as $r)
                <li class="flex flex-wrap items-center gap-2 rounded-xl bg-slate-50 px-4 py-3 ring-1 ring-slate-100">
                    <span class="font-semibold text-slate-900">{{ $r->slot->position->name }}</span>
                    <span class="text-slate-500">{{ $r->slot->date?->format('d/m/Y') }}</span>
                    <span class="text-slate-600">{{ substr((string) $r->slot->start_time, 0, 5) }}–{{ substr((string) $r->slot->end_time, 0, 5) }}</span>
                </li>
            @endforeach
        </ul>
        <p class="mt-8 text-sm text-slate-500">{{ __('Un email de confirmation vous a été envoyé.') }}</p>
        @if (! empty($participant_portal_url))
            <p class="mt-4 text-sm text-slate-600">
                <a href="{{ $participant_portal_url }}" class="font-semibold text-brand-700 underline">{{ __('Voir toutes mes inscriptions (lien personnel)') }}</a>
            </p>
        @endif
    </div>
    </div>
@endsection
