@php
    $registerUrl = route('public.event.register', $event->slug);
    $shareText = __('Salut ! On cherche des bénévoles pour :title — inscription ici :url', ['title' => $event->title, 'url' => route('public.event', $event->slug)]);
    $cf = $event->custom_fields ?? [];
    if (! is_array($cf)) {
        $cf = [];
    }
@endphp
<article class="public-card">
    <header class="border-b border-slate-100 pb-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-brand-700">{{ __('Inscription bénévoles') }}</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $event->title }}</h1>
            </div>
            <div class="flex flex-col items-end gap-2">
                <div class="rounded-xl bg-slate-50 px-4 py-2 text-right ring-1 ring-slate-200/80">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ __('Dates') }}</p>
                    <p class="text-sm font-semibold text-slate-800">
                        {{ $event->date_start?->format('d/m/Y') }} → {{ $event->date_end?->format('d/m/Y') }}
                    </p>
                </div>
                <button type="button" id="organizz-share-btn" class="rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-semibold text-white shadow hover:bg-brand-700">
                    {{ __('Partager') }}
                </button>
            </div>
        </div>
        @if ($event->description)
            <p class="mt-4 whitespace-pre-line text-base leading-relaxed text-slate-600">{{ $event->description }}</p>
        @endif
    </header>

    @if ($event->matching_enabled)
        <section class="mt-6 rounded-xl border border-amber-100 bg-amber-50/80 p-4 text-sm text-amber-950" id="matching-section" data-matching="1">
            <p class="font-semibold">{{ __('Filtrer par disponibilités') }}</p>
            <p class="mt-1 text-xs text-amber-900/80">{{ __('Cochez les demi-journées où vous êtes libre : seuls les créneaux compatibles restent visibles.') }}</p>
            <div class="mt-3 flex flex-wrap gap-2" id="availability-filters"></div>
        </section>
    @endif

    <section class="mt-8">
        <h2 class="text-lg font-semibold text-slate-900">{{ __('Créneaux disponibles') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('Ouvrez chaque jour pour voir le détail des postes et des horaires.') }}</p>

        @foreach ($days as $day => $rows)
            <details class="group slot-day mt-4 overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm open:shadow-card" data-day="{{ $day }}" @if ($loop->first) open @endif>
                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 bg-gradient-to-r from-slate-50 to-white px-5 py-4 font-semibold text-slate-900 transition hover:from-brand-50/50 hover:to-white [&::-webkit-details-marker]:hidden">
                    <span class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-100 text-brand-800">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        {{ \Carbon\Carbon::parse($day)->translatedFormat('l d F Y') }}
                    </span>
                    <svg class="h-5 w-5 shrink-0 text-slate-400 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <ul class="space-y-2 border-t border-slate-100 px-5 py-4">
                    @foreach ($rows as $row)
                        <li class="flex flex-wrap items-center gap-x-3 gap-y-1 rounded-xl bg-slate-50/80 px-4 py-3 text-sm ring-1 ring-slate-100">
                            <span class="inline-block h-3 w-3 shrink-0 rounded-full shadow-sm ring-2 ring-white" style="background: {{ $row['position_color'] }}"></span>
                            <span class="font-semibold text-slate-900">{{ $row['position'] }}</span>
                            <span class="text-slate-500">{{ $row['start_time'] }} – {{ $row['end_time'] }}</span>
                            @if ($row['full'] && empty($row['can_waitlist']))
                                <span class="ml-auto rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-800">{{ __('Complet') }}</span>
                            @elseif ($row['full'] && ! empty($row['can_waitlist']))
                                <span class="ml-auto rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-900">{{ __('Complet — liste d’attente') }}</span>
                            @else
                                <span class="ml-auto text-xs font-medium text-slate-500">{{ $row['active'] }}/{{ $row['max'] }} {{ __('places') }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </details>
        @endforeach
    </section>

    <section id="inscription-form" class="mt-10 scroll-mt-24 border-t border-slate-100 pt-10">
        <h2 class="text-lg font-semibold text-slate-900">{{ __('S’inscrire') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('Saisissez vos informations puis choisissez vos créneaux.') }}</p>

        @if ($errors->any())
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ $registerUrl }}" class="mt-6 space-y-5">
            @csrf
            <input type="text" name="company" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="firstname" class="public-label">{{ __('Prénom') }}</label>
                    <input id="firstname" name="firstname" value="{{ old('firstname') }}" required class="public-input">
                </div>
                <div>
                    <label for="lastname" class="public-label">{{ __('Nom') }}</label>
                    <input id="lastname" name="lastname" value="{{ old('lastname') }}" required class="public-input">
                </div>
            </div>
            <div>
                <label for="email" class="public-label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="public-input" autocomplete="email">
            </div>
            <div>
                <label for="phone" class="public-label">{{ __('Téléphone') }} <span class="font-normal text-slate-400">({{ __('optionnel, requis pour SMS') }})</span></label>
                <input id="phone" name="phone" inputmode="tel" value="{{ old('phone') }}" class="public-input" autocomplete="tel">
            </div>

            <div>
                <label for="preferred_reminder_channel" class="public-label">{{ __('Rappels') }}</label>
                <select id="preferred_reminder_channel" name="preferred_reminder_channel" class="public-input">
                    <option value="email" @selected(old('preferred_reminder_channel', 'email') === 'email')>{{ __('E-mail') }}</option>
                    <option value="sms" @selected(old('preferred_reminder_channel') === 'sms')>{{ __('SMS (si configuré par l’organisateur)') }}</option>
                    <option value="push" @selected(old('preferred_reminder_channel') === 'push')>{{ __('Notification navigateur (PWA)') }}</option>
                </select>
            </div>

            @foreach ($cf as $field)
                @if (is_array($field) && ! empty($field['id']))
                    @php
                        $fid = $field['id'];
                        $label = $field['label'] ?? $fid;
                        $type = $field['type'] ?? 'text';
                        $req = ! empty($field['required']);
                    @endphp
                    <div>
                        <label for="cf_{{ $fid }}" class="public-label">{{ $label }} @if ($req)<span class="text-rose-600">*</span>@endif</label>
                        @if ($type === 'select' && ! empty($field['options']) && is_array($field['options']))
                            <select id="cf_{{ $fid }}" name="custom_fields[{{ $fid }}]" class="public-input" @if ($req) required @endif>
                                <option value="">{{ __('— Choisir —') }}</option>
                                @foreach ($field['options'] as $opt)
                                    <option value="{{ $opt }}" @selected(old('custom_fields.'.$fid) === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        @else
                            <input id="cf_{{ $fid }}" name="custom_fields[{{ $fid }}]" value="{{ old('custom_fields.'.$fid) }}" class="public-input" @if ($req) required @endif>
                        @endif
                    </div>
                @endif
            @endforeach

            <fieldset>
                <legend class="public-label">{{ __('Créneaux choisis') }}</legend>
                <div class="mt-3 space-y-2">
                    @foreach ($days as $day => $rows)
                        <p class="slot-day-label pt-2 text-xs font-bold uppercase tracking-wider text-slate-400" data-day="{{ $day }}">{{ \Carbon\Carbon::parse($day)->format('d/m/Y') }}</p>
                        @foreach ($rows as $row)
                            @php
                                $startH = (int) substr($row['start_time'], 0, 2);
                                $half = $startH < 13 ? 'morning' : 'afternoon';
                            @endphp
                            <label
                                class="slot-row flex min-h-[3.25rem] cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white p-4 transition hover:border-brand-300 hover:bg-brand-50/30 @if ($row['full'] && empty($row['can_waitlist'])) cursor-not-allowed opacity-55 @endif"
                                data-day="{{ $day }}"
                                data-half="{{ $half }}"
                                data-start="{{ $row['start_time'] }}"
                            >
                                <input
                                    type="checkbox"
                                    name="slot_ids[]"
                                    value="{{ $row['slot_id'] }}"
                                    class="mt-1.5 h-5 w-5 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                    @if ($row['full'] && empty($row['can_waitlist'])) disabled @endif
                                    @if (collect(old('slot_ids', []))->contains($row['slot_id'])) checked @endif
                                >
                                <span class="text-sm leading-snug">
                                    <span class="font-semibold text-slate-900">{{ $row['position'] }}</span>
                                    <span class="text-slate-600"> — {{ $row['start_time'] }} – {{ $row['end_time'] }}</span>
                                    @if ($row['full'] && ! empty($row['can_waitlist']))
                                        <span class="ml-2 text-xs font-semibold text-amber-700">{{ __('Liste d’attente') }}</span>
                                    @elseif ($row['full'])
                                        <span class="ml-2 text-xs font-semibold text-rose-600">{{ __('Complet') }}</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    @endforeach
                </div>
            </fieldset>

            <div class="pt-2">
                <button type="submit" class="public-btn-primary w-full sm:w-auto">
                    {{ __('Valider mon inscription') }}
                </button>
            </div>
        </form>
    </section>
</article>

<script>
(function () {
    var shareBtn = document.getElementById('organizz-share-btn');
    var text = @json($shareText);
    var url = @json(route('public.event', $event->slug));
    var copiedLabel = @json(__('Lien copié !'));
    var shareLabel = @json(__('Partager'));
    if (shareBtn) {
        shareBtn.addEventListener('click', function () {
            if (navigator.share) {
                navigator.share({ title: document.title, text: text, url: url }).catch(function () {});
            } else if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text + '\n' + url).then(function () {
                    shareBtn.textContent = copiedLabel;
                    setTimeout(function () { shareBtn.textContent = shareLabel; }, 2500);
                });
            }
        });
    }

    var sec = document.getElementById('matching-section');
    if (!sec) return;
    var filt = sec.querySelector('#availability-filters');
    if (!filt) return;
    var byDay = {};
    document.querySelectorAll('.slot-row[data-day]').forEach(function (el) {
        var d = el.getAttribute('data-day');
        if (d) byDay[d] = true;
    });
    Object.keys(byDay).sort().forEach(function (d) {
        var sub = document.createElement('div');
        sub.className = 'mb-2 w-full text-[11px] font-semibold text-amber-900';
        sub.textContent = d;
        filt.appendChild(sub);
        [['morning', 'matin'], ['afternoon', 'après-midi']].forEach(function (pair) {
            var lab = document.createElement('label');
            lab.className = 'me-2 mb-1 inline-flex cursor-pointer items-center gap-1 rounded-lg bg-white px-2 py-1 text-xs ring-1 ring-amber-200';
            var inp = document.createElement('input');
            inp.type = 'checkbox';
            inp.className = 'rounded border-amber-300';
            inp.setAttribute('data-day', d);
            inp.setAttribute('data-half', pair[0]);
            lab.appendChild(inp);
            lab.appendChild(document.createTextNode(' ' + pair[1]));
            filt.appendChild(lab);
        });
    });

    function applyFilter() {
        var n = filt.querySelectorAll('input[type=checkbox]:checked').length;
        document.querySelectorAll('.slot-row[data-day]').forEach(function (el) {
            if (n === 0) {
                el.classList.remove('hidden');
                return;
            }
            var d = el.getAttribute('data-day');
            var half = el.getAttribute('data-half');
            var ok = filt.querySelector('input[data-day="' + d + '"][data-half="' + half + '"]:checked');
            el.classList.toggle('hidden', !ok);
        });
        document.querySelectorAll('details.slot-day[data-day]').forEach(function (det) {
            var has = det.querySelector('.slot-row:not(.hidden)');
            det.classList.toggle('hidden', n > 0 && !has);
        });
        document.querySelectorAll('p.slot-day-label[data-day]').forEach(function (p) {
            var d = p.getAttribute('data-day');
            var has = document.querySelector('.slot-row[data-day="' + d + '"]:not(.hidden)');
            p.classList.toggle('hidden', n > 0 && !has);
        });
    }
    filt.addEventListener('change', applyFilter);
})();
</script>
