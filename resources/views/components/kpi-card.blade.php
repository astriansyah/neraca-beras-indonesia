@props([
    'label',
    'value',
    'unit' => '',
    'icon' => 'chart-bar',
    'color' => 'emerald',
    'change' => null,
    'changeType' => 'positive', // positive, negative, neutral
    'sparklineId' => null,
    'estimate' => null,
])

@php
    $colorMap = [
        'emerald' => [
            'icon_bg' => 'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400',
            'change_pos' => 'text-emerald-600 dark:text-emerald-400',
        ],
        'amber' => [
            'icon_bg' => 'bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400',
            'change_pos' => 'text-amber-600 dark:text-amber-400',
        ],
        'sky' => [
            'icon_bg' => 'bg-sky-500/10 text-sky-600 dark:bg-sky-500/20 dark:text-sky-400',
            'change_pos' => 'text-sky-600 dark:text-sky-400',
        ],
        'purple' => [
            'icon_bg' => 'bg-purple-500/10 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400',
            'change_pos' => 'text-purple-600 dark:text-purple-400',
        ],
        'rose' => [
            'icon_bg' => 'bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400',
            'change_pos' => 'text-rose-600 dark:text-rose-400',
        ],
    ];

    $scheme = $colorMap[$color] ?? $colorMap['emerald'];

    $changeClass = match ($changeType) {
        'positive' => 'text-emerald-600 dark:text-emerald-400',
        'negative' => 'text-rose-600 dark:text-rose-400',
        'neutral' => 'text-slate-500 dark:text-slate-400',
        default => 'text-emerald-600 dark:text-emerald-400',
    };
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl bg-white ring-1 ring-slate-100 shadow-lg shadow-slate-200/60 hover:shadow-xl transition-all duration-300 dark:bg-slate-900 dark:ring-slate-800 dark:shadow-black/30 p-5 relative flex flex-col justify-between overflow-hidden']) }}>
    <div>
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                {{ $label }}
            </span>
            <span class="rounded-xl p-2.5 shrink-0 {{ $scheme['icon_bg'] }}">
                <x-icon :name="$icon" class="size-4" />
            </span>
        </div>

        <div class="mt-3">
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    {{ $value }}
                </span>
                @if ($unit)
                    <span class="text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400">
                        {{ $unit }}
                    </span>
                @endif
                @if ($estimate)
                    <x-estimate-icon :text="$estimate" />
                @endif
            </div>

            @if ($change)
                <div class="mt-2 flex items-center gap-1.5 text-xs font-semibold {{ $changeClass }}">
                    @if ($changeType === 'positive')
                        <x-icon name="trending-up" class="size-3.5 shrink-0" />
                    @elseif ($changeType === 'negative')
                        <x-icon name="trending-down" class="size-3.5 shrink-0" />
                    @endif
                    <span>{{ $change }}</span>
                </div>
            @endif
        </div>
    </div>

    @if ($sparklineId)
        <div class="mt-3 -mx-2 -mb-2 h-10 relative">
            <div id="{{ $sparklineId }}" class="w-full h-full"></div>
        </div>
    @endif
</div>
