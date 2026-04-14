@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-semibold text-white shadow-soft bg-gradient-to-r from-indigo-600 to-violet-600 ring-1 ring-white/20 transition'
    : 'inline-flex items-center whitespace-nowrap rounded-full px-3.5 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-900/5 hover:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
