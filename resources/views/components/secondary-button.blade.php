<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex min-h-[44px] items-center justify-center rounded-2xl border border-slate-200/90 bg-white/90 px-6 py-2.5 text-sm font-bold text-slate-800 shadow-soft backdrop-blur-sm transition hover:border-slate-300 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 touch-manipulation']) }}>
    {{ $slot }}
</button>
