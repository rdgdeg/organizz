@extends('layouts.public')

@section('title', __('Mes inscriptions'))

@section('content')
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-12">
        <article class="public-card">
            <h1 class="text-2xl font-bold text-slate-900">{{ __('Mes inscriptions') }}</h1>
            <p class="mt-2 text-sm text-slate-600">{{ $email }}</p>

            @if (session('success'))
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-900">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-900">
                    @foreach ($errors->all() as $e)
                        <p>{{ $e }}</p>
                    @endforeach
                </div>
            @endif

            <ul class="mt-8 space-y-4">
                @forelse ($registrations as $r)
                    @php
                        $ev = $r->slot->position->event;
                        $canEdit = $ev->canParticipantEditNow();
                    @endphp
                    <li class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 text-sm">
                        <p class="font-semibold text-slate-900">{{ $ev->title }}</p>
                        <p class="mt-1 text-slate-600">
                            {{ $r->slot->position->name }} · {{ $r->slot->date?->format('d/m/Y') }}
                            · {{ substr((string) $r->slot->start_time, 0, 5) }}–{{ substr((string) $r->slot->end_time, 0, 5) }}
                        </p>
                        @if ($r->waitlist)
                            <p class="mt-2 text-xs font-semibold text-amber-800">{{ __('Liste d’attente') }}</p>
                        @endif
                        @if ($canEdit)
                            <form method="post" action="{{ route('participant.annuler', ['token' => $token]) }}" class="mt-3" onsubmit="return confirm(@json(__('Annuler ce créneau ?')));">
                                @csrf
                                <input type="hidden" name="registration_id" value="{{ $r->id }}">
                                <button type="submit" class="text-sm font-medium text-rose-700 underline hover:text-rose-900">{{ __('Annuler ce créneau') }}</button>
                            </form>
                        @else
                            <p class="mt-2 text-xs text-slate-500">{{ __('Les modifications ne sont plus possibles (délai dépassé).') }}</p>
                        @endif
                    </li>
                @empty
                    <li class="text-slate-600">{{ __('Aucune inscription active.') }}</li>
                @endforelse
            </ul>
        </article>
    </div>
@endsection
