@extends('layouts.public')

@section('title', $event->title)

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 pb-28 sm:px-6 sm:py-12 md:pb-12">
        @include('public.event-content')
    </div>

    <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-slate-200/90 bg-white/95 px-4 py-3 backdrop-blur-md md:hidden" style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom))">
        <a href="#inscription-form" class="landing-btn-primary w-full justify-center shadow-lg shadow-brand-600/20">
            {{ __("Aller au formulaire d'inscription") }}
        </a>
    </div>
@endsection
