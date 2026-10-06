<x-layouts.app :page="$page" :page-key="$pageKey">
    <script>window.__NB_BALANCE__ = @json($stats);</script>
    <div x-data="{
        stats: window.__NB_BALANCE__,
        sankeyYear: '2025',
    }">
        <x-page-header eyebrow="Keseimbangan Pasokan &amp; Penggunaan" :title="$page['title']"
            :lead="$page['description']" :icon="$page['icon']" />

        {{-- Discrepancy Note Banner: Surplus Dokumen vs Hitungan --}}
        <div
            class="mt-6 rounded-2xl border border-amber-500/20 bg-amber-50/50 p-4 sm:p-5 dark:border-amber-500/30 dark:bg-amber-950/20 flex flex-col sm:flex-row items-start gap-4">
            <span
                class="rounded-xl bg-amber-500/10 p-2.5 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 shrink-0">
                <x-icon name="alert" class="size-5" />
            </span>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-sm font-bold text-amber-900 dark:text-amber-200">
                        Catatan Rekonsiliasi: Surplus Dokumen (3,52 jt ton) vs Hitungan Produksi–Konsumsi (3,61 jt ton)
                    </h2>
                    <span
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-200 text-amber-900 dark:bg-amber-900 dark:text-amber-200">
                        Selisih 0,09 Jt Ton
                    </span>
                </div>
                <p class="text-xs text-amber-800/90 dark:text-amber-300 leading-relaxed">
                    Siaran pers resmi Sekretariat Kabinet (Setkab) mencatat surplus beras 2025 sebesar <strong>3,52 juta
                        ton</strong>. Namun, selisih matematis antara Produksi Beras (34,71 juta ton) dan Konsumsi Resmi
                    (31,10 juta ton) menghasilkan surplus <strong>3,61 juta ton</strong>. Selisih 0,09 juta ton (90 ribu
                    ton) kemungkinan besar berasal dari pembulatan desimal atau asumsi konsumsi dasar sebesar ~31,19
                    juta ton. Dashboard menyajikan kedua angka ini secara objektif.
                </p>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-kpi-card label="Surplus Neraca 2024" value="+9,08" unit="juta ton" icon="scale" color="slate"
                change="Produksi + Impor − Konsumsi" change-type="neutral" />

            <x-kpi-card label="Surplus Neraca 2025" value="+3,61" unit="juta ton" icon="bowl" color="emerald"
                change="Versi Dokumen: +3,52 juta ton" change-type="positive"
                estimate="Selisih 0,09 juta ton akibat rekonsiliasi data konsumsi Setkab vs BPS." />

            <x-kpi-card label="Rasio Swasembada (SSR)" value="98,72" unit="%" icon="shield" color="emerald"
                change="+11,58% poin YoY (2024: 87,14%)" change-type="positive" />

            <x-kpi-card label="Kecukupan Domestik" value="111,61" unit="%" icon="chart-bar" color="emerald"
                change="Produksi ÷ Konsumsi (2024: 117,50%)" change-type="positive" />
        </div>

        {{-- Chart 1: DIAGRAM SANKEY D3.JS INTERAKTIF (Full Width) --}}
        <div class="mt-8">
            <x-chart-card id="chart-balance-sankey" title="Diagram Aliran Rantai Pasok Beras Indonesia"
                subtitle="Visualisasi perpindahan volume beras dari sumber pasokan (produksi &amp; impor) ke alokasi penggunaan (konsumsi, stok, dan residual)"
                badge="" source="Model Rekonsiliasi Neraca Beras (Bagian B)"
                method-info="Diagram Sankey memvisualisasikan konservasi massa aliran komoditas beras: Pasokan (Produksi + Impor) mengalir ke Pasokan Tersedia, kemudian didistribusikan ke Konsumsi Nasional, Ekspor, Akumulasi Stok Bulog, dan Residual non-Bulog."
                type="container" height="h-96 sm:h-[480px]">
                <x-slot:controls>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-700 dark:text-slate-300">Pilih Tahun Aliran:</span>
                            <div
                                class="inline-flex rounded-xl border border-slate-200 bg-slate-100 p-1 dark:border-slate-700 dark:bg-slate-800">
                                <button type="button"
                                    @click="sankeyYear = '2024'; window.dispatchEvent(new CustomEvent('nb-sankey-year', { detail: { year: '2024' } }))"
                                    :class="sankeyYear === '2024' ? 'bg-white text-emerald-700 font-bold shadow-xs dark:bg-slate-700 dark:text-emerald-300' : 'text-slate-600 dark:text-slate-400'"
                                    class="rounded-lg px-3 py-1 text-xs transition">
                                    Tahun 2024 (Impor 4,52 Jt Ton)
                                </button>
                                <button type="button"
                                    @click="sankeyYear = '2025'; window.dispatchEvent(new CustomEvent('nb-sankey-year', { detail: { year: '2025' } }))"
                                    :class="sankeyYear === '2025' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-400'"
                                    class="rounded-lg px-3 py-1 text-xs transition">
                                    Tahun 2025 (Swasembada 98,7%)
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-[11px] text-slate-500 dark:text-slate-400">
                            <span class="inline-flex items-center gap-1.5"><span
                                    class="size-2.5 rounded-full bg-emerald-500"></span> Inflow Pasokan</span>
                            <span class="inline-flex items-center gap-1.5"><span
                                    class="size-2.5 rounded-full bg-amber-500"></span> Konsumsi</span>
                            <span class="inline-flex items-center gap-1.5"><span
                                    class="size-2.5 rounded-full bg-purple-500"></span> Stok Bulog</span>
                            <span class="inline-flex items-center gap-1.5"><span
                                    class="size-2.5 rounded-full bg-slate-400"></span> Residual</span>
                        </div>
                    </div>
                </x-slot:controls>
                <x-slot:table>
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr
                                class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                <th class="py-2 px-3 font-semibold">Komponen Aliran</th>
                                <th class="py-2 px-3 font-semibold">Tahun 2024</th>
                                <th class="py-2 px-3 font-semibold">Tahun 2025</th>
                                <th class="py-2 px-3 font-semibold">Perubahan YoY</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium text-slate-800 dark:text-slate-200">Produksi
                                    Beras Domestik</td>
                                <td class="py-2 px-3">30,62 juta ton</td>
                                <td class="py-2 px-3">34,71 juta ton</td>
                                <td class="py-2 px-3 text-emerald-600 font-semibold">+13,36%</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium text-slate-800 dark:text-slate-200">Volume
                                    Impor</td>
                                <td class="py-2 px-3">4,52 juta ton</td>
                                <td class="py-2 px-3">0,45 juta ton</td>
                                <td class="py-2 px-3 text-emerald-600 font-semibold">−90,03%</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium text-slate-800 dark:text-slate-200">Konsumsi
                                    Nasional</td>
                                <td class="py-2 px-3">26,06 juta ton</td>
                                <td class="py-2 px-3">31,10 juta ton</td>
                                <td class="py-2 px-3 text-amber-600 font-semibold">+19,34%</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium text-slate-800 dark:text-slate-200">Akumulasi
                                    Cadangan Bulog (ΔStok)</td>
                                <td class="py-2 px-3">0,67 juta ton</td>
                                <td class="py-2 px-3">1,25 juta ton</td>
                                <td class="py-2 px-3 text-purple-600 font-semibold">+86,57%</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium text-slate-800 dark:text-slate-200">Residual
                                    Pasar Bebas &amp; Susut</td>
                                <td class="py-2 px-3">8,41 juta ton</td>
                                <td class="py-2 px-3">2,36 juta ton</td>
                                <td class="py-2 px-3 text-slate-500 font-semibold">−71,94%</td>
                            </tr>
                        </tbody>
                    </table>
                </x-slot:table>
            </x-chart-card>
        </div>

        {{-- Charts Row 2: Waterfall Chart & Radar Chart --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            {{-- Chart 2: ApexCharts Waterfall (Neraca Kotor 2024 vs 2025) --}}
            <x-chart-card id="chart-balance-waterfall" title="Dekomposisi Neraca Kotor Beras 2025"
                subtitle="Penambahan pasokan vs pengurangan penggunaan menuju saldo surplus akhir (juta ton)" badge=""
                source="Analisis Rumus: Neraca = Produksi + Impor − Konsumsi − Ekspor"
                method-info="Grafik waterfall memperlihatkan kontribusi positif dan negatif setiap elemen terhadap total surplus kotor 2025."
                type="container" height="h-80 sm:h-96">
                <x-slot:table>
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr
                                class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                <th class="py-2 px-3 font-semibold">Komponen Neraca</th>
                                <th class="py-2 px-3 font-semibold">Arah Aliran</th>
                                <th class="py-2 px-3 font-semibold">Nilai (Juta Ton)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium">Produksi Domestik</td>
                                <td class="py-2 px-3 text-emerald-600">+ Pasokan</td>
                                <td class="py-2 px-3 text-emerald-600 font-bold">+34,71</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium">Impor Beras</td>
                                <td class="py-2 px-3 text-sky-600">+ Pasokan</td>
                                <td class="py-2 px-3 text-sky-600 font-bold">+0,45</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium">Konsumsi Nasional</td>
                                <td class="py-2 px-3 text-amber-600">− Penggunaan</td>
                                <td class="py-2 px-3 text-amber-600 font-bold">−31,10</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium">Ekspor Beras</td>
                                <td class="py-2 px-3 text-rose-600">− Penggunaan</td>
                                <td class="py-2 px-3 text-rose-600 font-bold">−0,00006</td>
                            </tr>
                            <tr class="bg-emerald-500/10">
                                <td class="py-2 px-3 font-sans font-bold text-emerald-800 dark:text-emerald-200">Surplus
                                    Neraca Kotor</td>
                                <td class="py-2 px-3 text-emerald-700 dark:text-emerald-300">= Saldo Bersih</td>
                                <td class="py-2 px-3 text-emerald-700 dark:text-emerald-300 font-extrabold">+3,61</td>
                            </tr>
                        </tbody>
                    </table>
                </x-slot:table>
            </x-chart-card>

            {{-- Chart 3: Chart.js Radar Chart (Indeks Ketahanan Pangan Komposit) --}}
            <x-chart-card id="chart-balance-radar" title="Indeks Kedaulatan &amp; Ketahanan Beras"
                subtitle="Perbandingan multi-aksis ketahanan pangan nasional: 2024 vs 2025" badge=""
                source="Sintesis Indikator FAO, BPS, dan Kementan"
                method-info="Skala dinormalisasi (0–100%) meliputi: Swasembada (SSR), Kemerdekaan Impor (100-IDR), Kecukupan Domestik (Prod/Kons), Keamanan Cadangan (Cakupan CBP), dan Ketahanan Produksi."
                type="canvas" height="h-80 sm:h-96">
                <x-slot:table>
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr
                                class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                <th class="py-2 px-3 font-semibold">Dimensi Kedaulatan</th>
                                <th class="py-2 px-3 font-semibold">Tahun 2024</th>
                                <th class="py-2 px-3 font-semibold">Tahun 2025</th>
                                <th class="py-2 px-3 font-semibold">Status Capaian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium">Swasembada Beras (SSR)</td>
                                <td class="py-2 px-3">87,14%</td>
                                <td class="py-2 px-3 text-emerald-600 font-bold">98,72%</td>
                                <td class="py-2 px-3 font-sans text-emerald-600">Mendekati 100%</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium">Kemerdekaan Impor (100−IDR)</td>
                                <td class="py-2 px-3">87,14%</td>
                                <td class="py-2 px-3 text-emerald-600 font-bold">98,72%</td>
                                <td class="py-2 px-3 font-sans text-emerald-600">Ketergantungan minimal</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium">Kecukupan Pasokan (Prod/Kons)</td>
                                <td class="py-2 px-3">117,50%</td>
                                <td class="py-2 px-3 text-emerald-600 font-bold">111,61%</td>
                                <td class="py-2 px-3 font-sans text-emerald-600">Surplus mandiri</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium">Keamanan Cadangan Bulog</td>
                                <td class="py-2 px-3">30,7% (0,9 bln)</td>
                                <td class="py-2 px-3 text-purple-600 font-bold">41,7% (1,25 bln)</td>
                                <td class="py-2 px-3 font-sans text-purple-600">Menguat signifikan</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans font-medium">Kemandirian Pasokan Lokal</td>
                                <td class="py-2 px-3">85,0%</td>
                                <td class="py-2 px-3 text-emerald-600 font-bold">98,0%</td>
                                <td class="py-2 px-3 font-sans text-emerald-600">Rekor serapan petani</td>
                            </tr>
                        </tbody>
                    </table>
                </x-slot:table>
            </x-chart-card>
        </div>
    </div>
</x-layouts.app>