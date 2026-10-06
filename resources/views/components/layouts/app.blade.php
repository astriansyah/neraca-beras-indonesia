@props(['page', 'pageKey'])
@php
    $pages = config('dashboard.pages');
    $siteName = 'Neraca Beras Indonesia';
    $fullTitle = $pageKey === 'dashboard' ? $page['title'] . ' — Dashboard Data Interaktif' : $page['title'] . ' · ' . $siteName;
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $page['description'] }}">
    <meta name="theme-color" content="#059669">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $page['description'] }}">
    <meta property="og:type" content="website">
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='9' fill='%23059669'/><path d='M16 25V14m0 0c0-4-3-7-8-7 0 4.5 3 7 8 7Zm0 0c0-4.5 3.5-8 9-8 0 5-3.5 8-9 8Z' stroke='%23fbbf24' stroke-width='2.4' fill='none' stroke-linecap='round'/></svg>">
    <script>
        // Terapkan tema & status sidebar sebelum render (hindari kedip/CLS).
        (function () {
            try {
                var t = localStorage.getItem('nb-theme');
                if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
                if (localStorage.getItem('nb-sidebar') === 'collapsed') {
                    document.documentElement.setAttribute('data-sb', 'collapsed');
                }
            } catch (e) { }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js', $page['entry']])
</head>

<body class="page-bg min-h-full font-sans text-slate-800 antialiased dark:text-slate-200" x-data="{ mobileOpen: false }"
    @keydown.escape.window="mobileOpen = false">

    <a href="#konten"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow-lg">Lewati
        ke konten</a>

    {{-- Overlay mobile --}}
    <div x-cloak x-show="mobileOpen" x-transition.opacity @click="mobileOpen = false"
        class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

    {{-- ============ SIDEBAR ============ --}}
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200/70 bg-white/90 backdrop-blur-xl transition-[transform,width] duration-300
              lg:translate-x-0 lg:sb-collapsed:w-20 dark:border-slate-800 dark:bg-slate-900/90"
        :class="mobileOpen && 'translate-x-0! shadow-2xl'" aria-label="Navigasi utama">
        <div class="flex h-16 shrink-0 items-center gap-3 px-5 lg:sb-collapsed:justify-center lg:sb-collapsed:px-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3" aria-label="{{ $siteName }} — beranda">
                <span
                    class="grid size-9 shrink-0 place-items-center rounded-xl bg-linear-to-br from-emerald-500 to-emerald-700 text-amber-300 shadow-lg shadow-emerald-600/30">
                    <x-icon name="sprout" class="size-5" />
                </span>
                <span class="leading-tight lg:sb-collapsed:hidden">
                    <span class="block text-sm font-extrabold tracking-tight text-slate-900 dark:text-white">Neraca
                        Beras</span>
                    <span class="block text-[11px] font-medium text-slate-500 dark:text-slate-400">Indonesia
                        2024–2025</span>
                </span>
            </a>
            <button type="button" class="icon-btn ml-auto lg:hidden" @click="mobileOpen = false"
                aria-label="Tutup navigasi">
                <x-icon name="x" />
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4" aria-label="Halaman">
            <p
                class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 lg:sb-collapsed:hidden">
                Jelajahi data</p>
            @foreach ($pages as $key => $p)
                <a href="{{ route($p['route']) }}" id="nav-{{ $key }}"
                    class="nav-link lg:sb-collapsed:justify-center lg:sb-collapsed:px-0" @if ($key === $pageKey)
                    aria-current="page" @endif title="{{ $p['label'] }}">
                    <x-icon :name="$p['icon']" class="size-5 shrink-0" />
                    <span class="truncate lg:sb-collapsed:sr-only">{{ $p['label'] }}</span>
                </a>
                @if ($key === 'simulation')
                    <div class="my-3 border-t border-slate-200/70 dark:border-slate-800"></div>
                @endif
            @endforeach
        </nav>

        <div class="border-t border-slate-200/70 p-3 dark:border-slate-800">
            <button type="button" id="btn-collapse-sidebar"
                class="nav-link hidden w-full lg:flex lg:sb-collapsed:justify-center lg:sb-collapsed:px-0" x-data
                @click="$store.ui.toggleSidebar()" aria-controls="sidebar" :aria-expanded="!$store.ui.collapsed">
                <x-icon name="chevrons-left" class="size-5 shrink-0 transition-transform lg:sb-collapsed:rotate-180" />
                <span class="lg:sb-collapsed:sr-only">Ciutkan menu</span>
            </button>
            <p class="px-3 pt-2 text-[11px] leading-snug text-slate-400 lg:sb-collapsed:hidden">
                Situs publik &amp; read-only. Data dari BPS, Kementan, Bulog, dan media.
            </p>
        </div>
    </aside>

    {{-- ============ KONTEN ============ --}}
    <div class="flex min-h-screen flex-col transition-[padding] duration-300 lg:pl-72 lg:sb-collapsed:pl-20">
        <header
            class="sticky top-0 z-30 border-b border-slate-200/60 bg-white/70 backdrop-blur-xl dark:border-slate-800/80 dark:bg-slate-950/70">
            <div class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4 sm:px-6 lg:px-8">
                <button type="button" id="btn-open-sidebar" class="icon-btn lg:hidden" @click="mobileOpen = true"
                    aria-label="Buka navigasi" aria-controls="sidebar" :aria-expanded="mobileOpen">
                    <x-icon name="menu" />
                </button>
                <div class="min-w-0">
                    <p class="truncate text-xs font-medium text-slate-500 dark:text-slate-400">Neraca Beras Indonesia
                    </p>
                    <p class="truncate text-sm font-bold text-slate-900 dark:text-white">{{ $page['label'] }}</p>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    {{ $actions ?? '' }}
                    <button type="button" id="btn-theme" x-data @click="$store.ui.toggleTheme()"
                        class="icon-btn ring-1 ring-slate-200 dark:ring-slate-700"
                        :aria-label="$store.ui.dark ? 'Gunakan mode terang' : 'Gunakan mode gelap'"
                        aria-label="Ganti tema">
                        <x-icon name="moon" class="size-[18px] dark:hidden" />
                        <x-icon name="sun" class="hidden size-[18px] dark:block" />
                    </button>
                </div>
            </div>
        </header>

        <main id="konten" class="mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        <footer class="border-t border-slate-200/70 bg-white/60 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid gap-8 lg:grid-cols-3">
                    <div>
                        <p class="flex items-center gap-2 text-sm font-extrabold text-slate-900 dark:text-white">
                            <x-icon name="sprout" class="size-5 text-emerald-600" /> {{ $siteName }} 2024–2025
                        </p>
                        <p class="mt-3 text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                            Dashboard data publik untuk membaca neraca beras nasional. Semua angka berasal dari sumber
                            resmi dan media
                            yang dikutip di bawah; analisis turunan bersifat indikatif (hanya dua titik waktu).
                        </p>
                        <a href="{{ route('methodology') }}"
                            class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-emerald-700 hover:underline dark:text-emerald-400">
                            Metodologi &amp; kualitas data <span aria-hidden="true">→</span>
                        </a>
                    </div>
                    <div class="lg:col-span-2">
                        <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Sumber data</h2>
                        <ul class="mt-3 grid gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                            @foreach ($footerSources ?? [] as $s)
                                <li class="min-w-0">
                                    <a href="{{ $s['url'] }}" target="_blank" rel="noopener noreferrer"
                                        class="group flex items-start gap-1.5 text-slate-600 hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400">
                                        <x-icon name="external"
                                            class="mt-0.5 size-3.5 shrink-0 opacity-60 group-hover:opacity-100" />
                                        <span class="min-w-0"><span class="font-semibold">{{ $s['publisher'] }}</span> —
                                            {{ $s['title'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <p
                    class="mt-10 border-t border-slate-200/70 pt-6 text-xs text-slate-500 dark:border-slate-800 dark:text-slate-400">
                    © {{ date('Y') }} Neraca Beras Indonesia · Dibangun dengan Laravel, Chart.js, ApexCharts &amp; D3.js
                    · Akbar Baruni Einstein & Ahmad Syarifuddin Triansyah
                </p>
            </div>
        </footer>
    </div>

    <div id="nb-tooltip" class="nb-tooltip" style="opacity:0" role="status" aria-live="polite"></div>
</body>

</html>