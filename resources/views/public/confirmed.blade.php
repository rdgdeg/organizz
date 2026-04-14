@extends('layouts.public')

@section('title', __('Confirmation'))

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-12">
    <div class="public-card text-center">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-100 text-brand-700">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h1 class="mt-6 text-xl font-bold text-slate-900">{{ __('Inscription confirmée') }}</h1>
        <p class="mt-2 font-medium text-slate-800">{{ $event->title }}</p>
        <p class="mt-4 text-sm text-slate-600">{{ __('Merci, votre participation est bien enregistrée.') }}</p>
    </div>
    </div>
@endsection
