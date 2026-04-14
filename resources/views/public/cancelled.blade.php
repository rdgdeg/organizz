@extends('layouts.public')

@section('title', __('Annulation'))

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-12">
    <div class="public-card text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <h1 class="mt-6 text-xl font-bold text-slate-900">{{ $already ? __('Déjà annulé') : __('Inscription annulée') }}</h1>
        <p class="mt-2 text-slate-600">{{ __('Événement : :title', ['title' => $event->title]) }}</p>
    </div>
    </div>
@endsection
