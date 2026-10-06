<x-layouts.app :page="$page" :page-key="$pageKey">
    <x-page-header eyebrow="Analisis Probabilistik & Ketidakpastian" :title="$page['title']"
        :lead="$page['description']" :icon="$page['icon']" />

    {{-- Banner Peringatan Metodologis Monte Carlo --}}
    <div
        class="rounded-2xl border border-amber-200/80 bg-gradient-to-r from-amber-50/90 via-amber-50/50 to-orange-50/40 p-4 shadow-xs dark:border-amber-500/20 dark:from-amber-950/30 dark:via-amber-900/10 dark:to-orange-950/20">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 rounded-lg bg-amber-500/10 p-1.5 text-amber-600 dark:text-amber-400">
                <x-icon name="alert-triangle" class="size-5" />
            </div>
            <div class="space-y-1 text-xs text-amber-900 dark:text-amber-200">
                <p class="font-bold">Prinsip Pemodelan Stokastik: Bukan Peramalan Deterministik</p>
                <p class="leading-relaxed text-amber-800/90 dark:text-amber-300/80">
                    Simulasi Monte Carlo (10.000 iterasi) ini menguji rentang sensitivitas neraca beras tahun 2025
                    dengan memperlakukan rendemen gabah ke beras (distribusi triangular 55,0% s/d 62,0% dengan modus
                    57,65%) dan konsumsi nasional (26,06 s/d 31,50 juta ton dengan modus 31,10 juta ton) sebagai
                    variabel acak stokastik. Tujuannya adalah memetakan spektrum risiko defisit dan kecukupan cadangan
                    pangan untuk perumusan kebijakan kontinjensi.
                </p>
            </div>
        </div>
    </div>

    {{-- Ringkasan KPI Hasil Simulasi (Reaktif Real-time) --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div
            class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rata-rata
                Surplus (Mean)</p>
            <p id="kpi-sim-mean"
                class="mt-1 text-2xl font-extrabold tracking-tight text-emerald-600 dark:text-emerald-400 tabular-nums">
                +3,61 jt ton</p>
            <span id="kpi-sim-mean-sub"
                class="mt-1 inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                Saldo surplus riil nasional
            </span>
        </div>

        <div
            class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Median Surplus
                (P50)</p>
            <p id="kpi-sim-p50"
                class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white tabular-nums">+3,59 jt
                ton</p>
            <span
                class="mt-1 inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                Nilai tengah sebaran 50%
            </span>
        </div>

        <div
            class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Probabilitas
                Surplus</p>
            <p id="kpi-sim-prob-surplus"
                class="mt-1 text-2xl font-extrabold tracking-tight text-emerald-600 dark:text-emerald-400 tabular-nums">
                99,8%</p>
            <span id="kpi-sim-prob-deficit"
                class="mt-1 inline-flex items-center gap-1 text-[11px] font-semibold text-rose-500 dark:text-rose-400">
                P(Defisit) &lt; 0,2%
            </span>
        </div>

        <div
            class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Interval
                Kepercayaan 90% (P5–P95)</p>
            <p id="kpi-sim-ci"
                class="mt-1 text-lg font-extrabold tracking-tight text-slate-800 dark:text-slate-200 tabular-nums">+1,85
                s/d +5,42 jt ton</p>
            <span
                class="mt-1 inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                Rentang variabilitas empiris
            </span>
        </div>

        <div
            class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rata-rata SSR
                (Swasembada)</p>
            <p id="kpi-sim-ssr"
                class="mt-1 text-2xl font-extrabold tracking-tight text-emerald-600 dark:text-emerald-400 tabular-nums">
                98,7%</p>
            <span
                class="mt-1 inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                Pangsa produksi domestik
            </span>
        </div>
    </div>

    {{-- Grid 2 Kolom: Panel Input What-If Sliders vs Histogram D3.js --}}
    <div class="grid gap-6 lg:grid-cols-12">
        {{-- Kolom Kiri: Kontrol What-If Skenario (5 cols) --}}
        <div class="space-y-6 lg:col-span-5">
            <x-card>
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 dark:text-white">Panel Kontrol Skenario</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Ubah parameter asumsi untuk mengamati
                            pergeseran kurva risiko secara instan.</p>
                    </div>
                    <button id="btn-reset-sim" type="button" class="btn-ghost text-xs px-2.5 py-1.5"
                        title="Kembalikan ke asumsi default pemerintah">
                        <x-icon name="rotate-ccw" class="size-3.5" /> Reset
                    </button>
                </div>

                {{-- Preset Skenario Cepat --}}
                <div class="mt-4">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Preset Skenario
                        Kebijakan</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" data-preset="baseline"
                            class="preset-btn rounded-xl border border-emerald-500/40 bg-emerald-50/70 p-2 text-left text-xs transition hover:bg-emerald-100 dark:border-emerald-500/30 dark:bg-emerald-950/30 dark:hover:bg-emerald-900/40 ring-2 ring-emerald-500">
                            <span class="font-bold text-emerald-800 dark:text-emerald-300 block">Baseline
                                Pemerintah</span>
                            <span class="text-[10px] text-emerald-700/80 dark:text-emerald-400/70">GKG 60,21 • Rendemen
                                57,65% • Konsumsi 31,10</span>
                        </button>
                        <button type="button" data-preset="optimistic"
                            class="preset-btn rounded-xl border border-slate-200 bg-slate-50 p-2 text-left text-xs transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/50 dark:hover:bg-slate-800">
                            <span class="font-bold text-slate-800 dark:text-slate-200 block">Swasembada Total</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400">GKG 63,50 • Rendemen 60,00% •
                                Impor 0</span>
                        </button>
                        <button type="button" data-preset="pessimistic"
                            class="preset-btn rounded-xl border border-slate-200 bg-slate-50 p-2 text-left text-xs transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/50 dark:hover:bg-slate-800">
                            <span class="font-bold text-slate-800 dark:text-slate-200 block">Guncangan Iklim</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400">GKG 54,00 • Rendemen 55,00% •
                                Impor 2,50</span>
                        </button>
                        <button type="button" data-preset="bps_low"
                            class="preset-btn rounded-xl border border-slate-200 bg-slate-50 p-2 text-left text-xs transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/50 dark:hover:bg-slate-800">
                            <span class="font-bold text-slate-800 dark:text-slate-200 block">Konsumsi BPS Agregat</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400">GKG 60,21 • Rendemen 57,65% •
                                Konsumsi 26,06</span>
                        </button>
                    </div>
                </div>

                {{-- Slider Inputs --}}
                <div class="mt-6 space-y-4">
                    {{-- 1. Produksi GKG --}}
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold text-slate-700 dark:text-slate-300">1. Produksi Gabah Kering Giling
                                (GKG)</span>
                            <span class="font-extrabold text-emerald-600 dark:text-emerald-400 tabular-nums"><span
                                    id="val-gkg">60,21</span> juta ton</span>
                        </div>
                        <input id="slider-gkg" type="range" class="range" min="50.0" max="68.0" step="0.1"
                            value="60.21" />
                        <div class="flex justify-between text-[10px] text-slate-400 mt-0.5">
                            <span>50,00 jt ton (Krisis)</span>
                            <span>Default: 60,21</span>
                            <span>68,00 jt ton (Rekor)</span>
                        </div>
                    </div>

                    {{-- 2. Rendemen Beras (Mode) --}}
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold text-slate-700 dark:text-slate-300">2. Rendemen Beras Paling Mungkin
                                (Mode)</span>
                            <span class="font-extrabold text-emerald-600 dark:text-emerald-400 tabular-nums"><span
                                    id="val-rendemen">57,65</span>%</span>
                        </div>
                        <input id="slider-rendemen" type="range" class="range" min="54.0" max="62.0" step="0.05"
                            value="57.65" />
                        <div class="flex justify-between text-[10px] text-slate-400 mt-0.5">
                            <span>Min: 55,0%</span>
                            <span>Standar BPS: 57,65%</span>
                            <span>Maks: 62,0%</span>
                        </div>
                    </div>

                    {{-- 3. Konsumsi Beras Nasional (Mode) --}}
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold text-slate-700 dark:text-slate-300">3. Kebutuhan Konsumsi Nasional
                                (Mode)</span>
                            <span class="font-extrabold text-sky-600 dark:text-sky-400 tabular-nums"><span
                                    id="val-konsumsi">31,10</span> juta ton</span>
                        </div>
                        <input id="slider-konsumsi" type="range" class="range" min="25.0" max="34.0" step="0.1"
                            value="31.10" />
                        <div class="flex justify-between text-[10px] text-slate-400 mt-0.5">
                            <span>26,06 jt ton (BPS)</span>
                            <span>Default Bapanas: 31,10</span>
                            <span>34,00 jt ton (Tinggi)</span>
                        </div>
                    </div>

                    {{-- 4. Kuota Impor Beras --}}
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold text-slate-700 dark:text-slate-300">4. Kuota Realisasi Impor
                                Beras</span>
                            <span class="font-extrabold text-amber-600 dark:text-amber-400 tabular-nums"><span
                                    id="val-impor">0,45</span> juta ton</span>
                        </div>
                        <input id="slider-impor" type="range" class="range" min="0.00" max="5.00" step="0.05"
                            value="0.45" />
                        <div class="flex justify-between text-[10px] text-slate-400 mt-0.5">
                            <span>0,00 (Nol Impor)</span>
                            <span>Target 2025: 0,45</span>
                            <span>5,00 jt ton (Maks 2024)</span>
                        </div>
                    </div>

                    {{-- 5. Stok Awal CBP Bulog --}}
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold text-slate-700 dark:text-slate-300">5. Cadangan Beras Pemerintah
                                (Stok Awal Bulog)</span>
                            <span class="font-extrabold text-indigo-600 dark:text-indigo-400 tabular-nums"><span
                                    id="val-stok">2,00</span> juta ton</span>
                        </div>
                        <input id="slider-stok" type="range" class="range" min="0.50" max="4.00" step="0.1"
                            value="2.00" />
                        <div class="flex justify-between text-[10px] text-slate-400 mt-0.5">
                            <span>0,50 jt ton</span>
                            <span>Awal 2025: 2,00</span>
                            <span>4,00 jt ton</span>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                        <span class="inline-block size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        10.000 iterasi Monte Carlo aktif
                    </span>
                    <button id="btn-re-run" type="button" class="btn-primary text-xs px-3 py-1.5 shadow-sm">
                        <x-icon name="play" class="size-3.5" /> Simulasi Ulang
                    </button>
                </div>
            </x-card>
        </div>

        {{-- Kolom Kanan: D3.js Histogram & Kurva Densitas (7 cols) --}}
        <div class="space-y-6 lg:col-span-7">
            <x-chart-card id="chart-sim-histogram" title="Distribusi Probabilitas Empiris Neraca Beras 2025"
                subtitle="Histogram frekuensi & kurva Kernel Density Estimation (KDE) 10.000 iterasi stokastik."
                badge="" source="Simulasi Monte Carlo (Model Triangular Rendemen & Konsumsi)"
                method-info="Distribusi triangular diterapkan pada rendemen beras r in [55,0%, 62,0%] dengan mode 57,65%, dan konsumsi C in [26,06, 31,50] dengan mode 31,10 juta ton. Area merah mengindikasikan risiko defisit (neraca kotor < 0)."
                type="container" height="h-80 sm:h-96">
                <x-slot:controls>
                    <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-4">
                            <span class="flex items-center gap-1.5 font-medium text-slate-600 dark:text-slate-300">
                                <span class="size-3 rounded bg-emerald-500"></span> Zona Surplus (&ge; 0)
                            </span>
                            <span class="flex items-center gap-1.5 font-medium text-slate-600 dark:text-slate-300">
                                <span class="size-3 rounded bg-rose-500"></span> Zona Defisit (&lt; 0)
                            </span>
                            <span class="flex items-center gap-1.5 font-medium text-slate-600 dark:text-slate-300">
                                <span class="h-0.5 w-4 bg-emerald-600 dark:bg-emerald-400"></span> Kurva Densitas KDE
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400">
                            Garis Putus-putus: <strong class="text-amber-500">P5</strong> • <strong
                                class="text-sky-500">Median (P50)</strong> • <strong
                                class="text-purple-500">P95</strong>
                        </div>
                    </div>
                </x-slot:controls>
            </x-chart-card>
        </div>
    </div>

    {{-- Baris Kedua: Kurva CDF ApexCharts & Diagram Sensitivitas --}}
    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Chart 2: Cumulative Distribution Function (CDF) --}}
        <x-chart-card id="chart-sim-cdf" title="Fungsi Distribusi Kumulatif (CDF Empiris)"
            subtitle="Probabilitas kumulatif neraca beras berada di bawah nilai ambang batas tertentu." badge=""
            source="Hasil 10.000 Run Monte Carlo Neraca Beras Indonesia"
            method-info="Kurva CDF memetakan nilai P(X <= x) dari 0% hingga 100%. Titik potong kurva dengan sumbu x=0 mencerminkan Value-at-Risk atau probabilitas terjadinya defisit neraca riil."
            type="container" height="h-80" />

        {{-- Chart 3: Diagram Sensitivitas (Tornado) --}}
        <x-chart-card id="chart-sim-sensitivity" title="Analisis Sensitivitas Parameter (Tornado Chart)"
            subtitle="Dampak perubahan relatif ±5% dari masing-masing variabel kunci terhadap saldo akhir neraca."
            badge="" source="Turunan Parsial Matematis Model Neraca Beras"
            method-info="Mengukur elastisitas absolut saldo neraca: variasi 5% pada Rendemen dan GKG memberikan pengaruh volumetrik terbesar (~1,73 juta ton), disusul oleh konsumsi nasional (~1,55 juta ton)."
            type="container" height="h-80" />
    </div>

    {{-- Tabel Ringkasan Statistik Persentil & Tombol Ekspor --}}
    <x-card>
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Tabel Distribusi Persentil &amp;
                    Ringkasan Statistik</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Ringkasan titik kuantil utama sebaran 10.000
                    iterasi Monte Carlo neraca beras 2025.</p>
            </div>
            <div class="flex items-center gap-2">
                <button id="btn-export-sim-csv" type="button" class="btn-ghost text-xs">
                    <x-icon name="download" class="size-3.5" /> Unduh Sampel CSV (1.000 Run)
                </button>
                <button id="btn-export-sim-json" type="button" class="btn-ghost text-xs">
                    <x-icon name="code" class="size-3.5" /> Unduh JSON Statistik
                </button>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Parameter Statistik</th>
                        <th class="num">Nilai (Juta Ton)</th>
                        <th>Keterangan Metodologis</th>
                        <th>Interpretasi Risiko Kebijakan</th>
                    </tr>
                </thead>
                <tbody id="sim-percentiles-body">
                    <tr>
                        <td class="font-bold">Minimum (P0)</td>
                        <td class="num font-mono text-rose-600 dark:text-rose-400" id="stat-p0">-0,85</td>
                        <td>Nilai terendah dari seluruh 10.000 iterasi simulasi</td>
                        <td>Skenario terburuk (Worst Case) jika rendemen jatuh ke 55% dan konsumsi melonjak</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Persentil 5 (P5)</td>
                        <td class="num font-mono text-amber-600 dark:text-amber-400" id="stat-p5">+1,85</td>
                        <td>Batas bawah interval kepercayaan 90%</td>
                        <td>Hanya ada 5% probabilitas bahwa surplus akan berada di bawah batas ini</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Kuartil 1 (P25)</td>
                        <td class="num font-mono" id="stat-p25">+2,95</td>
                        <td>25% iterasi berada di bawah nilai ini</td>
                        <td>Batas kuartil bawah kecukupan pasokan nasional</td>
                    </tr>
                    <tr class="bg-emerald-50/50 dark:bg-emerald-950/20 font-semibold">
                        <td class="text-emerald-900 dark:text-emerald-200">Median (P50)</td>
                        <td class="num font-mono text-emerald-700 dark:text-emerald-300" id="stat-p50">+3,59</td>
                        <td>Nilai tengah sebaran empiris (titik 50%)</td>
                        <td>Nilai estimasi paling representatif terhadap ketidakpastian simetris</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Rata-rata (Mean)</td>
                        <td class="num font-mono" id="stat-mean">+3,61</td>
                        <td>Ekspektasi matematis rata-rata populasi sebaran</td>
                        <td>Rata-rata tertimbang seluruh sebaran probabilitas</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Kuartil 3 (P75)</td>
                        <td class="num font-mono" id="stat-p75">+4,28</td>
                        <td>75% iterasi berada di bawah nilai ini</td>
                        <td>Kuartil atas surplus pangan nasional</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Persentil 95 (P95)</td>
                        <td class="num font-mono text-purple-600 dark:text-purple-400" id="stat-p95">+5,42</td>
                        <td>Batas atas interval kepercayaan 90%</td>
                        <td>95% simulasi berada di bawah angka ini (skenario pasokan tinggi)</td>
                    </tr>
                    <tr>
                        <td class="font-bold">Maksimum (P100)</td>
                        <td class="num font-mono text-emerald-600 dark:text-emerald-400" id="stat-p100">+6,35</td>
                        <td>Nilai tertinggi yang diamati dalam 10.000 run</td>
                        <td>Skenario terbaik (Best Case) dengan rendemen puncak 62%</td>
                    </tr>
                    <tr class="border-t-2 border-slate-200 dark:border-slate-700">
                        <td class="font-bold">Standar Deviasi (Simpangan Baku)</td>
                        <td class="num font-mono" id="stat-std">1,12</td>
                        <td>Ukuran dispersi ketidakpastian neraca</td>
                        <td>Tingkat ketidakpastian volumetrik terhadap angka proyeksi</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Oper data PHP ke JavaScript client-side --}}
    <script>
        window.__NB_SIMULATION__ = @json($stats);
    </script>
</x-layouts.app>