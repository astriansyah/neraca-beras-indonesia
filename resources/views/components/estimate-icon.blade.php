{{-- Ikon penanda data estimasi/rentang (is_estimate = true) dengan tooltip. --}}
@props(['text' => 'Estimasi / rentang'])
<span x-data="{ open: false }" class="relative inline-flex align-middle" @mouseenter="open = true" @mouseleave="open = false">
    <button type="button" @click="open = !open" @focus="open = true" @blur="open = false"
            class="inline-flex size-5 items-center justify-center rounded-full bg-amber-100 text-amber-700 ring-1 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30"
            aria-label="Data estimasi: {{ $text }}">
        <x-icon name="approx" class="size-3.5" />
    </button>
    <span x-cloak x-show="open" x-transition.opacity role="tooltip"
          class="absolute left-1/2 top-full z-40 mt-2 w-56 -translate-x-1/2 rounded-lg bg-slate-900 px-3 py-2 text-[11px] font-medium leading-snug text-white shadow-xl dark:bg-slate-700">
        {{ $text }}
    </span>
</span>
