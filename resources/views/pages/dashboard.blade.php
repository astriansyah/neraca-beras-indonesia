<x-layouts.app :page="$page" :page-key="$pageKey">
    <script>window.__NB_STATS__ = @json($stats);</script>
    <div x-data="{
        yearFilter: 'compare', // '2024', '2025', 'compare'
        setYear(y) {
            this.yearFilter = y;
            window.dispatchEvent(new CustomEvent('nb-year-filter', { detail: { year: y } }));
        }
    }">
        {{-- Page Header & Year Filter Toolbar --}}
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200/70 dark:border-slate-800">
            <div>
                <div
                    class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/20">
                    <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Dashboard Utama &amp; Ringkasan Strategis</span>
                </div>
                <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Neraca Beras Indonesia 2024–2025
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 max-w-2xl">
                    Sistem pemantauan dan analisis statistik komprehensif pasokan, konsumsi, perdagangan, dan cadangan
                    beras nasional berbasis data resmi BPS, Kementan, Setkab, dan Bulog.
                </p>
            </div>

            {{-- Year Selector Buttons --}}
            <div class="flex items-center gap-1.5 p-1 rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 self-start sm:self-auto shrink-0"
                role="group" aria-label="Filter Tahun">
                <button type="button" @click="setYear('2024')"
                    :class="yearFilter === '2024' ? 'bg-white text-emerald-700 shadow-sm font-bold dark:bg-slate-700 dark:text-emerald-300' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
                    class="px-3 py-1.5 text-xs rounded-lg transition" aria-pressed="false">
                    2024
                </button>
                <button type="button" @click="setYear('2025')"
                    :class="yearFilter === '2025' ? 'bg-white text-emerald-700 shadow-sm font-bold dark:bg-slate-700 dark:text-emerald-300' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
                    class="px-3 py-1.5 text-xs rounded-lg transition" aria-pressed="false">
                    2025
                </button>
                <button type="button" @click="setYear('compare')"
                    :class="yearFilter === 'compare' ? 'bg-white text-emerald-700 shadow-sm font-bold dark:bg-slate-700 dark:text-emerald-300' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
                    class="px-3 py-1.5 text-xs rounded-lg transition" aria-pressed="true">
                    Bandingkan
                </button>
            </div>
        </div>

        {{-- KPI Cards Grid (5 Metrics with Sparklines) --}}
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            {{-- 1. Produksi Beras --}}
            <x-kpi-card label="Produksi Beras" :value="isset($stats['kpis']['produksi_beras']['val_2025']) ? \fmt_id($stats['kpis']['produksi_beras']['val_2025'], 2) : '34,71'" unit="juta ton" icon="sprout"
                color="emerald" change="+13,36% YoY (2024: 30,62)" change-type="positive"
                sparkline-id="spark-produksi" />

            {{-- 2. Konsumsi Beras --}}
            <x-kpi-card label="Konsumsi Beras" :value="isset($stats['kpis']['konsumsi_total']['val_2025']) ? \fmt_id($stats['kpis']['konsumsi_total']['val_2025'], 2) : '31,10'" unit="juta ton" icon="bowl"
                color="amber" change="+19,34% YoY (2024: 26,06)" change-type="neutral" sparkline-id="spark-konsumsi"
                estimate="Rentang estimasi 30,00–31,00 juta ton; estimasi resmi ~31,10. Ada perbedaan cakupan metodologi." />

            {{-- 3. Impor Beras --}}
            <x-kpi-card label="Volume Impor" :value="isset($stats['kpis']['impor_volume']['val_2025']) ? \fmt_id($stats['kpis']['impor_volume']['val_2025'], 2) : '0,45'" unit="juta ton" icon="ship" color="sky"
                change="−90,03% YoY (2024: 4,52)" change-type="positive" sparkline-id="spark-impor" />

            {{-- 4. Stok CBP Bulog --}}
            <x-kpi-card label="Stok Akhir Bulog" :value="isset($stats['kpis']['stok_bulog']['val_2025']) ? \fmt_id($stats['kpis']['stok_bulog']['val_2025'], 2) : '3,25'" unit="juta ton" icon="warehouse"
                color="purple" change="+62,42% YoY (2024: 2,00)" change-type="positive" sparkline-id="spark-stok" />

            {{-- 5. Rasio Swasembada (SSR) --}}
            <x-kpi-card label="Rasio Swasembada" :value="isset($stats['kpis']['ssr']['val_2025']) ? \fmt_id($stats['kpis']['ssr']['val_2025'], 1) : '98,7'" unit="%" icon="shield" color="emerald"
                change="+11,6% poin (2024: 87,1%)" change-type="positive" sparkline-id="spark-ssr" />
        </div>

        {{-- Automated Narrative Insight Banner --}}
        <div
            class="mt-6 rounded-2xl border border-emerald-500/20 bg-linear-to-r from-emerald-500/5 via-emerald-500/10 to-amber-500/5 p-5 shadow-sm dark:border-emerald-500/30 dark:from-emerald-950/30 dark:via-emerald-900/20 dark:to-slate-900">
            <div class="flex items-start gap-3.5">
                <span class="rounded-xl bg-emerald-500 p-2 text-white shadow-md shadow-emerald-500/30 shrink-0">
                    <x-icon name="sparkles" class="size-5" />
                </span>
                <div class="space-y-1.5 flex-1">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                        {{ $stats['narrative']['headline'] ?? 'Produksi Beras Nasional 2025 Melonjak +13,36% YoY; Impor Ditekan Hingga −90,03%' }}
                    </h2>
                    <div
                        class="grid sm:grid-cols-2 gap-2 pt-1 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        @if (!empty($stats['narrative']['points']))
                            @foreach ($stats['narrative']['points'] as $point)
                                <div class="flex items-start gap-2">
                                    <x-icon name="check"
                                        class="size-4 shrink-0 text-emerald-600 dark:text-emerald-400 mt-0.5" />
                                    <span>{{ $point }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-start gap-2">
                                <x-icon name="check"
                                    class="size-4 shrink-0 text-emerald-600 dark:text-emerald-400 mt-0.5" />
                                <span>Produksi beras nasional meningkat dari 30,62 juta ton (2024) menjadi 34,71 juta ton
                                    (2025).</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <x-icon name="check"
                                    class="size-4 shrink-0 text-emerald-600 dark:text-emerald-400 mt-0.5" />
                                <span>Rasio Swasembada Beras (SSR) meningkat dari 87,1% menjadi 98,7%.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Visualizations Grid --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            {{-- Chart 1: Grouped Bar Chart (Chart.js) --}}
            <div class="lg:col-span-2">
                <x-chart-card id="chart-grouped-summary" title="Perbandingan Neraca Beras 2024 vs 2025"
                    subtitle="Volume Produksi Domestik, Konsumsi Nasional, Impor, dan Stok CBP Bulog (juta ton)"
                    badge="" source="BPS (KSA 2024–2025), Kementan, Setkab, Bulog" source-url="https://www.bps.go.id"
                    method-info="Data dihitung dari tabel resmi data_points. Menggunakan plugin custom shadow Chart.js dengan ketajaman warna dan efek depth."
                    type="canvas" height="h-80 sm:h-96">
                    <x-slot:table>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                    <th class="py-2 px-3 font-semibold">Komponen Neraca</th>
                                    <th class="py-2 px-3 font-semibold">Tahun 2024</th>
                                    <th class="py-2 px-3 font-semibold">Tahun 2025</th>
                                    <th class="py-2 px-3 font-semibold">Perubahan YoY</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium text-slate-800 dark:text-slate-200">
                                        Produksi Beras</td>
                                    <td class="py-2 px-3">30,62 juta ton</td>
                                    <td class="py-2 px-3">34,71 juta ton</td>
                                    <td class="py-2 px-3 text-emerald-600 font-semibold">+13,36%</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium text-slate-800 dark:text-slate-200">
                                        Konsumsi Beras</td>
                                    <td class="py-2 px-3">26,06 juta ton</td>
                                    <td class="py-2 px-3">31,10 juta ton</td>
                                    <td class="py-2 px-3 text-amber-600 font-semibold">+19,34%</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium text-slate-800 dark:text-slate-200">
                                        Volume Impor</td>
                                    <td class="py-2 px-3">4,52 juta ton</td>
                                    <td class="py-2 px-3">0,45 juta ton</td>
                                    <td class="py-2 px-3 text-emerald-600 font-semibold">−90,03%</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium text-slate-800 dark:text-slate-200">Stok
                                        Akhir Bulog (CBP)</td>
                                    <td class="py-2 px-3">2,00 juta ton</td>
                                    <td class="py-2 px-3">3,25 juta ton</td>
                                    <td class="py-2 px-3 text-purple-600 font-semibold">+62,42%</td>
                                </tr>
                            </tbody>
                        </table>
                    </x-slot:table>
                </x-chart-card>
            </div>

            {{-- Chart 2: RadialBar Chart (ApexCharts) SSR & IDR --}}
            <div>
                <x-chart-card id="chart-ssr-idr" title="Rasio Swasembada &amp; Impor"
                    subtitle="SSR (Kemandirian) vs IDR (Ketergantungan Impor)" badge=""
                    source="Analisis Rumus FAO: SSR = Prod / (Prod + Imp - Eksp) * 100"
                    method-info="SSR (Self-Sufficiency Ratio) mengukur proporsi konsumsi yang dipenuhi produksi dalam negeri. IDR (Import Dependency Ratio) mengukur ketergantungan impor."
                    type="container" height="h-80 sm:h-96">
                    <x-slot:table>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                    <th class="py-2 px-2 font-semibold">Indikator</th>
                                    <th class="py-2 px-2 font-semibold">2024</th>
                                    <th class="py-2 px-2 font-semibold">2025</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                                <tr>
                                    <td class="py-2 px-2 font-sans font-medium">SSR (Swasembada)</td>
                                    <td class="py-2 px-2 text-emerald-600 font-semibold">87,14%</td>
                                    <td class="py-2 px-2 text-emerald-600 font-semibold">98,72%</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-2 font-sans font-medium">IDR (Impor)</td>
                                    <td class="py-2 px-2 text-sky-600 font-semibold">12,86%</td>
                                    <td class="py-2 px-2 text-sky-600 font-semibold">1,28%</td>
                                </tr>
                            </tbody>
                        </table>
                    </x-slot:table>
                </x-chart-card>
            </div>
        </div>

        {{-- YoY Metric Comparison & Quality Notes --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            {{-- Chart 3: ApexCharts Bar Growth Comparison --}}
            <div class="lg:col-span-2">
                <x-chart-card id="chart-yoy-comparison" title="Laju Perubahan Indikator Strategis YoY (%)"
                    subtitle="Perbandingan pertumbuhan antar indikator: Produksi, Luas Panen, Produktivitas, Konsumsi, Impor, dan Stok Bulog"
                    badge="" source="Perhitungan RiceStatisticsService (Bagian A)"
                    method-info="Persentase YoY dihitung dengan rumus ((Nilai 2025 - Nilai 2024) / Nilai 2024) * 100. Kategori berlabel indikatif n=2."
                    type="container" height="h-72 sm:h-80">
                    <x-slot:table>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                    <th class="py-1.5 px-3 font-semibold">Indikator</th>
                                    <th class="py-1.5 px-3 font-semibold">2024</th>
                                    <th class="py-1.5 px-3 font-semibold">2025</th>
                                    <th class="py-1.5 px-3 font-semibold">YoY (%)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                                <tr>
                                    <td class="py-1.5 px-3 font-sans">Produksi GKG</td>
                                    <td class="py-1.5 px-3">53,14 jt ton</td>
                                    <td class="py-1.5 px-3">60,21 jt ton</td>
                                    <td class="py-1.5 px-3 text-emerald-600 font-semibold">+13,30%</td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-3 font-sans">Luas Panen</td>
                                    <td class="py-1.5 px-3">10,05 jt ha</td>
                                    <td class="py-1.5 px-3">11,32 jt ha</td>
                                    <td class="py-1.5 px-3 text-emerald-600 font-semibold">+12,64%</td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-3 font-sans">Produktivitas Lahan</td>
                                    <td class="py-1.5 px-3">5,29 ton/ha</td>
                                    <td class="py-1.5 px-3">5,32 ton/ha</td>
                                    <td class="py-1.5 px-3 text-emerald-600 font-semibold">+0,59%</td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-3 font-sans">Konsumsi Beras</td>
                                    <td class="py-1.5 px-3">26,06 jt ton</td>
                                    <td class="py-1.5 px-3">31,10 jt ton</td>
                                    <td class="py-1.5 px-3 text-amber-600 font-semibold">+19,34%</td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-3 font-sans">Volume Impor</td>
                                    <td class="py-1.5 px-3">4,52 jt ton</td>
                                    <td class="py-1.5 px-3">0,45 jt ton</td>
                                    <td class="py-1.5 px-3 text-emerald-600 font-semibold">−90,03%</td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-3 font-sans">Stok CBP Bulog</td>
                                    <td class="py-1.5 px-3">2,00 jt ton</td>
                                    <td class="py-1.5 px-3">3,25 jt ton</td>
                                    <td class="py-1.5 px-3 text-purple-600 font-semibold">+62,42%</td>
                                </tr>
                            </tbody>
                        </table>
                    </x-slot:table>
                </x-chart-card>
            </div>

            {{-- Data Integrity & Quality Badges Card --}}
            <x-card class="flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span
                            class="rounded-xl p-2 bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400">
                            <x-icon name="alert" class="size-4" />
                        </span>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Catatan Integritas Data</h3>
                    </div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Transparansi keterbatasan metodologis dan sifat estimasi dataset resmi 2024–2025.
                    </p>

                    <div class="mt-4 space-y-2.5">
                        @if (!empty($stats['warnings']))
                            @foreach ($stats['warnings'] as $warning)
                                <div
                                    class="rounded-xl border border-slate-200/80 bg-slate-50/70 p-3 text-xs leading-relaxed dark:border-slate-800 dark:bg-slate-800/40">
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <span
                                            class="font-bold text-slate-800 dark:text-slate-200">{{ $warning['title'] }}</span>
                                        <span
                                            class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200">
                                            {{ $warning['badge'] ?? 'Perhatian' }}
                                        </span>
                                    </div>
                                    <p class="text-slate-600 dark:text-slate-400 text-[11px]">{{ $warning['body'] }}</p>
                                </div>
                            @endforeach
                        @else
                            <div
                                class="rounded-xl border border-amber-200 bg-amber-50/60 p-3 text-xs text-amber-900 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-200">
                                <strong>Perbedaan Metodologi Konsumsi:</strong> Lonjakan konsumsi +19% (26,06 ke 31,10 jt
                                ton) dipengaruhi perluasan cakupan kebutuhan total nasional vs konsumsi rumah tangga.
                            </div>
                            <div
                                class="rounded-xl border border-sky-200 bg-sky-50/60 p-3 text-xs text-sky-900 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-200">
                                <strong>Stok Bulog ≠ Stok Nasional:</strong> Cadangan 3,25 jt ton merupakan kelolaan
                                pemerintah, belum mencakup penggilingan dan rumah tangga.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400">
                    <a href="{{ route('methodology') }}"
                        class="inline-flex items-center gap-1 font-semibold text-emerald-600 hover:underline dark:text-emerald-400">
                        <span>Baca metodologi &amp; rumus lengkap</span>
                        <x-icon name="external-link" class="size-3" />
                    </a>
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>