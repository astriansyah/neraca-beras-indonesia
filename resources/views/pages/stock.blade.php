<x-layouts.app :page="$page" :page-key="$pageKey">
    <script>window.__NB_STOCK__ = @json($stats);</script>
    <div x-data="{
        stats: window.__NB_STOCK__,
        skenario: '31_10', // '31_10' (Permintaan Agregat) or '26_06' (Rumah Tangga)
    }">
        <x-page-header eyebrow="Cadangan Pangan Pemerintah" :title="$page['title']" :lead="$page['description']"
            :icon="$page['icon']" />

        {{-- Methodology Warning Banner: CBP vs Total Stok Nasional --}}
        <div
            class="mt-6 rounded-2xl border border-purple-500/20 bg-purple-50/50 p-4 sm:p-5 dark:border-purple-500/30 dark:bg-purple-950/20 flex flex-col sm:flex-row items-start gap-4">
            <span
                class="rounded-xl bg-purple-500/10 p-2.5 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400 shrink-0">
                <x-icon name="alert" class="size-5" />
            </span>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-sm font-bold text-purple-900 dark:text-purple-200">
                        Catatan Ruang Lingkup: Stok Cadangan Beras Pemerintah (CBP) ≠ Total Stok Nasional
                    </h2>
                    <span
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-purple-200 text-purple-900 dark:bg-purple-900 dark:text-purple-200">
                        Definisi Data
                    </span>
                </div>
                <p class="text-xs text-purple-800/90 dark:text-purple-300 leading-relaxed">
                    Angka stok 2,00 juta ton (2024) dan 3,25 juta ton (2025) adalah <strong>Cadangan Beras Pemerintah
                        (CBP)</strong> yang dikelola Perum Bulog di gudang-gudang BUMN, <em>bukan</em> total stok fisik
                    seluruh Indonesia. Data ini belum memperhitungkan stok di pedagang besar, penggilingan, pasar
                    tradisional, dan rumah tangga konsumen (yang tercermin dalam residual neraca).
                </p>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-kpi-card label="Stok Awal CBP 2024" value="1,26–1,40" unit="juta ton" icon="scale" color="slate"
                change="Midpoint: 1,33 juta ton" change-type="neutral" estimate="Rentang estimasi awal tahun 2024" />

            <x-kpi-card label="Stok Akhir CBP 2024" value="2,00" unit="juta ton" icon="scale" color="purple"
                change="ΔStok: +0,67 juta ton YoY" change-type="positive" />

            <x-kpi-card label="Stok Akhir CBP 2025" value="3,25" unit="juta ton" icon="shield" color="purple"
                change="+62,42% YoY (Rekor sejak 1968)" change-type="positive" />

            <x-kpi-card label="Bulan Cakupan Stok 2025" value="1,25–1,50" unit="bulan" icon="clock" color="emerald"
                change="Target ideal FAO: 3,0 bulan" change-type="neutral" />
        </div>

        {{-- Charts Row 1: Timeline Akumulasi Stok Bulog & D3 Bullet Chart --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            {{-- Chart 1: Chart.js Line Akumulasi CBP --}}
            <div class="lg:col-span-2">
                <x-chart-card id="chart-stock-timeline" title="Tren Akumulasi Cadangan Beras Pemerintah (CBP) Bulog"
                    subtitle="Perjalanan cadangan beras dari awal 2024 hingga rekor stok akhir 2025 (juta ton)" badge=""
                    source="Perum Bulog, BPS, dan Kantor Komunikasi Kepresidenan (PCO)"
                    source-url="https://www.bulog.co.id"
                    method-info="Data mencakup titik awal 2024 (1,33 jt ton), penutupan 2024 (2,00 jt ton), dan penutupan 2025 (3,25 jt ton) dengan serapan beras lokal mencatat rekor 3,20 juta ton."
                    type="canvas" height="h-72 sm:h-80">
                    <x-slot:table>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                    <th class="py-2 px-3 font-semibold">Periode Cadangan</th>
                                    <th class="py-2 px-3 font-semibold">Volume (Juta Ton)</th>
                                    <th class="py-2 px-3 font-semibold">Kategori Status</th>
                                    <th class="py-2 px-3 font-semibold">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Awal Tahun 2024</td>
                                    <td class="py-2 px-3">1,33</td>
                                    <td class="py-2 px-3 text-amber-600 font-semibold">Estimasi Rentang</td>
                                    <td class="py-2 px-3 font-sans text-slate-500">Rentang 1,26–1,40 juta ton</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Akhir Tahun 2024</td>
                                    <td class="py-2 px-3">2,00</td>
                                    <td class="py-2 px-3 text-purple-600 font-semibold">Realisasi CBP</td>
                                    <td class="py-2 px-3 font-sans text-slate-500">Stok akhir per 31 Desember 2024</td>
                                </tr>
                                <tr class="bg-purple-500/10">
                                    <td class="py-2 px-3 font-sans font-bold text-purple-800 dark:text-purple-200">Akhir
                                        Tahun 2025</td>
                                    <td class="py-2 px-3 font-extrabold text-purple-800 dark:text-purple-200">3,25</td>
                                    <td class="py-2 px-3 text-emerald-600 font-bold">Rekor Tertinggi</td>
                                    <td class="py-2 px-3 font-sans text-slate-700 dark:text-slate-300">Serapan lokal
                                        3,20 jt ton; total kelolaan 4,35 jt ton</td>
                                </tr>
                            </tbody>
                        </table>
                    </x-slot:table>
                </x-chart-card>
            </div>

            {{-- Chart 2: D3.js Bullet Chart Bulan Cakupan --}}
            <div>
                <x-chart-card id="chart-stock-bullet" title="Bulan Cakupan Stok Bulog"
                    subtitle="Ketahanan stok terhadap kebutuhan konsumsi bulanan" badge=""
                    source="Analisis Rumus: Stok ÷ (Konsumsi Tahunan ÷ 12)"
                    method-info="Bulan cakupan mengukur berapa bulan kebutuhan konsumsi nasional dapat dipenuhi oleh stok CBP. Skenario 1 memakai basis 26,06 jt ton (2,17 jt ton/bln = 1,50 bulan). Skenario 2 memakai basis 31,10 jt ton (2,59 jt ton/bln = 1,25 bulan). Target rekomendasi FAO adalah 3 bulan."
                    type="container" height="h-72 sm:h-80">
                    <x-slot:controls>
                        <div class="flex items-center justify-between gap-2 pt-1 text-xs">
                            <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">Pilih Basis
                                Konsumsi:</span>
                            <div
                                class="inline-flex rounded-lg border border-slate-200 bg-slate-100 p-0.5 dark:border-slate-700 dark:bg-slate-800">
                                <button type="button"
                                    @click="skenario = '31_10'; window.dispatchEvent(new CustomEvent('nb-stock-scenario', { detail: { scenario: '31_10' } }))"
                                    :class="skenario === '31_10' ? 'bg-white text-purple-700 font-bold shadow-xs dark:bg-slate-700 dark:text-purple-300' : 'text-slate-600 dark:text-slate-400'"
                                    class="rounded-md px-2 py-1 text-[11px] transition">
                                    31,10 Jt Ton (1,25 bln)
                                </button>
                                <button type="button"
                                    @click="skenario = '26_06'; window.dispatchEvent(new CustomEvent('nb-stock-scenario', { detail: { scenario: '26_06' } }))"
                                    :class="skenario === '26_06' ? 'bg-white text-purple-700 font-bold shadow-xs dark:bg-slate-700 dark:text-purple-300' : 'text-slate-600 dark:text-slate-400'"
                                    class="rounded-md px-2 py-1 text-[11px] transition">
                                    26,06 Jt Ton (1,50 bln)
                                </button>
                            </div>
                        </div>
                    </x-slot:controls>
                    <x-slot:table>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                    <th class="py-2 px-3 font-semibold">Titik Waktu</th>
                                    <th class="py-2 px-3 font-semibold">Skenario 26,06 Jt Ton</th>
                                    <th class="py-2 px-3 font-semibold">Skenario 31,10 Jt Ton</th>
                                    <th class="py-2 px-3 font-semibold">Target Standar FAO</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Awal 2024 (1,33 jt ton)</td>
                                    <td class="py-2 px-3">0,61 bulan</td>
                                    <td class="py-2 px-3">0,51 bulan</td>
                                    <td class="py-2 px-3 text-slate-400">3,00 bulan</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Akhir 2024 (2,00 jt ton)</td>
                                    <td class="py-2 px-3">0,92 bulan</td>
                                    <td class="py-2 px-3">0,77 bulan</td>
                                    <td class="py-2 px-3 text-slate-400">3,00 bulan</td>
                                </tr>
                                <tr class="bg-purple-500/10">
                                    <td class="py-2 px-3 font-sans font-bold text-purple-800 dark:text-purple-200">Akhir
                                        2025 (3,25 jt ton)</td>
                                    <td class="py-2 px-3 font-bold text-emerald-600">1,50 bulan</td>
                                    <td class="py-2 px-3 font-bold text-purple-600">1,25 bulan</td>
                                    <td class="py-2 px-3 font-bold text-slate-800 dark:text-slate-200">3,00 bulan</td>
                                </tr>
                            </tbody>
                        </table>
                    </x-slot:table>
                </x-chart-card>
            </div>
        </div>

        {{-- Charts Row 2: Dekomposisi Perubahan Stok & Residual Neraca --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            {{-- Chart 3: ApexCharts Bar Perubahan Stok vs Residual --}}
            <div class="lg:col-span-2">
                <x-chart-card id="chart-stock-decomposition"
                    title="Alokasi Surplus: Penambahan Stok Bulog vs Residual Tak Terjelaskan"
                    subtitle="Membongkar distribusi surplus neraca kotor ke dalam cadangan pemerintah vs pasar bebas"
                    badge="" source="Analisis Integrasi Neraca Beras &amp; Data Bulog 2024–2025"
                    method-info="Surplus Neraca Kotor = ΔStok Bulog + Residual. Residual mencerminkan stok di penggilingan, pedagang, susut pascapanen, dan pakan/industri."
                    type="container" height="h-72 sm:h-80">
                    <x-slot:table>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                    <th class="py-2 px-3 font-semibold">Komponen Alokasi</th>
                                    <th class="py-2 px-3 font-semibold">Tahun 2024 (Juta Ton)</th>
                                    <th class="py-2 px-3 font-semibold">Tahun 2025 (Juta Ton)</th>
                                    <th class="py-2 px-3 font-semibold">Karakteristik</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Penambahan Stok CBP Bulog (ΔStok)</td>
                                    <td class="py-2 px-3 text-purple-600 font-bold">+0,67</td>
                                    <td class="py-2 px-3 text-purple-600 font-bold">+1,25</td>
                                    <td class="py-2 px-3 font-sans text-slate-500">Cadangan fisik milik pemerintah</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Residual Tak Terjelaskan (Non-Bulog)
                                    </td>
                                    <td class="py-2 px-3 text-amber-600 font-bold">+8,41</td>
                                    <td class="py-2 px-3 text-amber-600 font-bold">+2,36</td>
                                    <td class="py-2 px-3 font-sans text-slate-500">Pedagang, penggilingan, susut rantai
                                        pasok</td>
                                </tr>
                                <tr class="bg-emerald-500/10">
                                    <td class="py-2 px-3 font-sans font-bold text-emerald-800 dark:text-emerald-200">
                                        Total Surplus Neraca Kotor</td>
                                    <td class="py-2 px-3 font-bold text-emerald-800 dark:text-emerald-200">+9,08</td>
                                    <td class="py-2 px-3 font-bold text-emerald-800 dark:text-emerald-200">+3,61</td>
                                    <td class="py-2 px-3 font-sans text-emerald-700 dark:text-emerald-300 font-medium">
                                        Produksi + Impor − Konsumsi</td>
                                </tr>
                            </tbody>
                        </table>
                    </x-slot:table>
                </x-chart-card>
            </div>

            {{-- Strategic Insight Card --}}
            <div
                class="rounded-2xl bg-white p-5 sm:p-6 ring-1 ring-slate-100 shadow-lg shadow-slate-200/60 dark:bg-slate-900 dark:ring-slate-800 dark:shadow-black/30 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span
                            class="grid size-8 place-items-center rounded-xl bg-purple-500/10 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400">
                            <x-icon name="shield" class="size-4" />
                        </span>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Dinamika Serapan Rekor 2025</h3>
                    </div>

                    <div class="mt-4 space-y-3 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50/70 p-3 dark:border-slate-800 dark:bg-slate-800/50">
                            <span class="font-bold text-slate-800 dark:text-slate-200 block mb-1">Serapan Dalam Negeri
                                Rekor</span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Penyerapan gabah/beras petani domestik mencapai <strong>3,20 juta ton</strong>,
                                tertinggi sepanjang sejarah operasional Bulog sejak tahun 1968.
                            </p>
                        </div>

                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50/70 p-3 dark:border-slate-800 dark:bg-slate-800/50">
                            <span class="font-bold text-slate-800 dark:text-slate-200 block mb-1">Total Kelolaan
                                Beras</span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                Total kelolaan beras sepanjang tahun 2025 tercatat sebesar <strong>4,35 juta
                                    ton</strong>, menggabungkan stok sisa 2024, serapan lokal, dan impor terencana.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400">
                    Sumber: Pernyataan resmi Perum Bulog &amp; Badan Pangan Nasional (Bapanas).
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>