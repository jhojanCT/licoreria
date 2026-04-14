@php
    $alerts = collect();

    if (session('success')) {
        $alerts->push(['type' => 'success', 'message' => session('success')]);
    }
    if (session('error')) {
        $alerts->push(['type' => 'error', 'message' => session('error')]);
    }
    if (session('warning')) {
        $alerts->push(['type' => 'warning', 'message' => session('warning')]);
    }
    if (session('info')) {
        $alerts->push(['type' => 'info', 'message' => session('info')]);
    }
    if ($errors->any()) {
        $alerts->push([
            'type' => 'error',
            'message' => $errors->count() === 1
                ? $errors->first()
                : 'Se encontraron '.$errors->count().' errores. Revisa los campos marcados.',
        ]);
    }
@endphp

@if ($alerts->isNotEmpty())
    <div class="mb-5 space-y-2.5">
        @foreach ($alerts as $index => $alert)
            @php
                $isSuccess = $alert['type'] === 'success';
                $isError = $alert['type'] === 'error';
                $isWarning = $alert['type'] === 'warning';

                $containerClasses = $isSuccess
                    ? 'border-emerald-200/70 bg-gradient-to-br from-emerald-50/95 to-white text-emerald-950 ring-emerald-900/5'
                    : ($isError
                        ? 'border-rose-200/80 bg-gradient-to-br from-rose-50/95 to-white text-rose-950 ring-rose-900/5'
                        : ($isWarning
                            ? 'border-amber-200/80 bg-gradient-to-br from-amber-50/95 to-white text-amber-950 ring-amber-900/5'
                            : 'border-sky-200/80 bg-gradient-to-br from-sky-50/95 to-white text-sky-950 ring-sky-900/5'));
                $iconWrapClasses = $isSuccess
                    ? 'bg-emerald-500/15 text-emerald-600'
                    : ($isError
                        ? 'bg-rose-500/15 text-rose-600'
                        : ($isWarning ? 'bg-amber-500/15 text-amber-600' : 'bg-sky-500/15 text-sky-600'));
            @endphp
            <div
                x-data="{ open: true }"
                x-init="setTimeout(() => open = false, 7000)"
                x-show="open"
                x-transition.opacity.duration.250ms
                role="alert"
                class="flex gap-3 rounded-2xl border px-4 py-3.5 text-sm shadow-soft ring-1 {{ $containerClasses }}"
            >
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $iconWrapClasses }}">
                    @if ($isSuccess)
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    @elseif ($isError)
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z"/></svg>
                    @elseif ($isWarning)
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01m-8.99 4h17.98c1.66 0 2.7-1.8 1.86-3.24L13.86 4.76c-.83-1.43-2.89-1.43-3.72 0L1.14 16.76C.3 18.2 1.34 20 3.01 20z"/></svg>
                    @else
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </span>

                <div class="min-w-0 flex-1">
                    <p class="pt-1 leading-relaxed">{{ $alert['message'] }}</p>
                </div>

                <button
                    type="button"
                    class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-black/5 text-current/70 transition hover:bg-black/10 hover:text-current"
                    @click="open = false"
                    aria-label="Cerrar alerta"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endforeach
    </div>
@endif
