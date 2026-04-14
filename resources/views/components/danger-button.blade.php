<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-[44px] items-center justify-center rounded-xl border border-transparent bg-gradient-to-r from-rose-600 to-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:from-rose-500 hover:to-red-500 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 active:opacity-90 touch-manipulation']) }}>
    {{ $slot }}
</button>
