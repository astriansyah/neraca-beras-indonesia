@props([
    'id',
    'title',
    'subtitle' => null,
    'source' => null,
    'sourceUrl' => null,
    'badge' => null,
    'methodInfo' => null,
    'height' => 'h-72 sm:h-80',
    'type' => 'container', // 'canvas' for Chart.js, 'container' for ApexCharts / D3
])

<div x-data="{
        loading: false,
        showTable: false,
        showInfo: false,
        chartId: '{{ $id }}',
        downloadPng() {
            if (window.nbCharts && typeof window.nbCharts.downloadPng === 'function') {
                window.nbCharts.downloadPng(this.chartId, '{{ \Illuminate\Support\Str::slug($title) }}');
            }
        }
     }"
     {{ $attributes->merge(['class' => 'rounded-2xl bg-white ring-1 ring-slate-100 shadow-lg shadow-slate-200/60 hover:shadow-xl transition-all duration-300 dark:bg-slate-900 dark:ring-slate-800 dark:shadow-black/30 p-5 sm:p-6 flex flex-col justify-between']) }}>

    {{-- Card Header --}}
    <div>
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-tight">
                        {!! $title !!}
                    </h3>
                    @if ($badge)
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/20">
                            {{ $badge }}
                        </span>
                    @endif
                </div>
                @if ($subtitle)
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-2xl">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            {{-- Action Toolbar --}}
            <div class="flex items-center gap-1.5 shrink-0">
                @if ($methodInfo)
                    <button type="button"
                            @click="showInfo = !showInfo"
                            class="inline-flex size-8 items-center justify-center rounded-lg border border-slate-200/80 bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-400 dark:hover:bg-slate-700 dark:hover:text-white transition"
                            title="Informasi Metodologi & Rumus"
                            aria-label="Informasi Metodologi">
                        <x-icon name="info" class="size-4" />
                    </button>
                @endif

                <button type="button"
                        @click="showTable = !showTable"
                        :class="showTable ? 'bg-emerald-50 text-emerald-700 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-700' : 'bg-slate-50 text-slate-500 border-slate-200/80 hover:bg-slate-100 hover:text-slate-800 dark:bg-slate-800/60 dark:text-slate-400 dark:border-slate-800 dark:hover:bg-slate-700 dark:hover:text-white'"
                        class="inline-flex items-center gap-1 rounded-lg border px-2.5 py-1.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                        title="Beralih ke tampilan tabel data"
                        aria-label="Lihat tabel data">
                    <x-icon name="table" class="size-3.5" />
                    <span class="hidden sm:inline" x-text="showTable ? 'Tutup Tabel' : 'Tabel Data'">Tabel Data</span>
                </button>

                <button type="button"
                        @click="downloadPng()"
                        class="inline-flex items-center gap-1 rounded-lg border border-slate-200/80 bg-slate-50 px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-white transition"
                        title="Unduh visualisasi ini sebagai gambar PNG"
                        aria-label="Unduh chart PNG">
                    <x-icon name="download" class="size-3.5" />
                    <span class="hidden sm:inline">Unduh PNG</span>
                </button>
            </div>
        </div>

        {{-- Methodology Alert Box --}}
        @if ($methodInfo)
            <div x-cloak x-show="showInfo" x-transition
                 class="mt-3 rounded-xl border border-sky-200/80 bg-sky-50/80 p-3 text-xs leading-relaxed text-sky-900 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-200 flex items-start gap-2">
                <x-icon name="info" class="size-4 shrink-0 mt-0.5 text-sky-600 dark:text-sky-400" />
                <div class="flex-1">
                    <span class="font-bold block mb-0.5">Catatan Metodologi:</span>
                    <span>{{ $methodInfo }}</span>
                </div>
                <button type="button" @click="showInfo = false" class="text-sky-500 hover:text-sky-800 dark:hover:text-sky-100">
                    <x-icon name="x" class="size-3.5" />
                </button>
            </div>
        @endif
    </div>

    {{-- Chart & Table Body --}}
    <div class="relative mt-5 flex-1 min-h-[200px]">
        {{-- Skeleton Loading --}}
        <div x-show="loading" class="absolute inset-0 flex flex-col justify-center items-center gap-3 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xs z-10 rounded-xl">
            <div class="size-8 animate-spin rounded-full border-3 border-emerald-500 border-t-transparent"></div>
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Memuat visualisasi data...</span>
        </div>

        {{-- Main Chart Visualizer --}}
        <div x-show="!showTable" class="w-full h-full flex items-center justify-center">
            @if ($type === 'canvas')
                <div class="w-full relative {{ $height }}">
                    <canvas id="{{ $id }}" class="w-full h-full block"></canvas>
                </div>
            @else
                <div id="{{ $id }}" class="w-full {{ $height }} relative"></div>
            @endif
        </div>

        {{-- Accessible Alternate Data Table --}}
        <div x-cloak x-show="showTable" x-transition class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800 p-2 bg-slate-50/50 dark:bg-slate-950/40">
            @if (isset($table))
                {{ $table }}
            @else
                <div id="{{ $id }}-table" class="text-xs text-slate-600 dark:text-slate-300">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>

    {{-- Extra Slot for Controls/Sliders under the chart --}}
    @if (isset($controls))
        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
            {{ $controls }}
        </div>
    @endif

    {{-- Card Footer --}}
    @if ($source || isset($footer))
        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-400 dark:text-slate-500">
            @if ($source)
                <div class="flex items-center gap-1.5 truncate max-w-full">
                    <span class="font-medium">Sumber:</span>
                    @if ($sourceUrl)
                        <a href="{{ $sourceUrl }}" target="_blank" rel="noopener noreferrer" class="text-emerald-600 hover:underline dark:text-emerald-400 truncate inline-flex items-center gap-1">
                            <span>{{ $source }}</span>
                            <x-icon name="external-link" class="size-3 shrink-0" />
                        </a>
                    @else
                        <span class="truncate">{{ $source }}</span>
                    @endif
                </div>
            @endif

            @if (isset($footer))
                <div>{{ $footer }}</div>
            @endif
        </div>
    @endif
</div>
