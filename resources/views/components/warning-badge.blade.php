{{-- Badge peringatan kualitas data. $note: array dari tabel notes (title, body, badge, severity). --}}
@props(['note', 'compact' => false])
@php
    $sev = $note['severity'] ?? 'info';
    $styles = [
        'danger' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-500/30',
        'warning' => 'bg-amber-50 text-amber-800 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/30',
        'info' => 'bg-sky-50 text-sky-700 ring-sky-200 dark:bg-sky-500/10 dark:text-sky-300 dark:ring-sky-500/30',
    ][$sev] ?? '';
@endphp
<span x-data="{ open: false }" class="relative inline-flex" @mouseenter="open = true" @mouseleave="open = false">
    <button type="button" @click="open = !open" @focus="open = true" @blur="open = false"
            {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 transition {$styles}"]) }}
            aria-label="Catatan kualitas data: {{ $note['title'] }}">
        <x-icon name="alert" class="size-3.5 shrink-0" />
        @unless ($compact)<span class="text-left">{{ $note['badge'] ?? $note['title'] }}</span>@endunless
    </button>
    <span x-cloak x-show="open" x-transition.opacity role="tooltip"
          class="absolute left-0 top-full z-40 mt-2 w-72 max-w-[80vw] rounded-xl bg-white p-3 text-xs leading-relaxed text-slate-600 shadow-xl ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700">
        <span class="block font-bold text-slate-900 dark:text-white">{{ $note['title'] }}</span>
        <span class="mt-1 block">{{ $note['body'] }}</span>
    </span>
</span>
