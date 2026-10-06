@props(['eyebrow' => null, 'title', 'lead' => null, 'icon' => null])
<header {{ $attributes->merge(['class' => 'animate-fade-up mb-8']) }}>
    @if ($eyebrow)
        <p class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/20">
            @if ($icon)<x-icon :name="$icon" class="size-3.5" />@endif
            {{ $eyebrow }}
        </p>
    @endif
    <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl dark:text-white">{{ $title }}</h1>
    @if ($lead)
        <p class="mt-3 max-w-3xl text-base leading-relaxed text-slate-600 sm:text-lg dark:text-slate-300">{{ $lead }}</p>
    @endif
    {{ $slot }}
</header>
