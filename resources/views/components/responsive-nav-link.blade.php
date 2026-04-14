@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block w-full border-l-4 border-indigo-600 bg-indigo-50 py-2.5 ps-4 pe-4 text-start text-base font-semibold text-indigo-900 transition'
    : 'block w-full border-l-4 border-transparent py-2.5 ps-4 pe-4 text-start text-base font-medium text-slate-600 transition hover:border-slate-200 hover:bg-slate-50 hover:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
