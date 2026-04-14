@props(['value'])

<label {{ $attributes->merge(['class' => 'mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500']) }}>
    {{ $value ?? $slot }}
</label>
