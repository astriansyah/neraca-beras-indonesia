<x-layouts.app :page="$page" :page-key="$pageKey">
    <div x-data="{
        perKapitaBasis: 'neraca', // 'neraca' (92.4) or 'publikasi' (79.078)
        total2024: 26.06,
        populasiAcuan: 284,
        get perKapitaVal() {
            return this.perKapitaBasis === 'neraca' ? 92.40 : 79.078;
        },
        get impliedPopulasi() {
            return ((this.total2024 * 1000) / this.perKapitaVal).toFixed(2);
        },
        get popSelisih() {
            return (this.impliedPopulasi - 282.0).toFixed(2);
        },
        get popStatus() {
            return this.perKapitaBasis === 'neraca' ? 'Konsisten' : 'Inkonsisten';
        }
    }">
        <x-page-header eyebrow="Analisis Sektor Konsumsi" :title="$page['title']" :lead="$page['description']"
            :icon="$page['icon']" />

        {{-- Methodology Discrepancy Warning Banner --}}
        <div
            class="mt-6 rounded-2xl border border-rose-500/20 bg-rose-50/50 p-4 sm:p-5 dark:border-rose-500/30 dark:bg-rose-950/20 flex flex-col sm:flex-row items-start gap-4">
            <span class="rounded-xl bg-rose-500/10 p-2.5 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 shrink-0">
                <x-icon name="alert" class="size-5" />
            </span>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-sm font-bold text-rose-900 dark:text-rose-200">
                        Catatan Kritis Kualitas Data: Perbedaan Metodologi Konsumsi Antar Tahun
                    </h2>
                    <span
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-200 text-rose-900 dark:bg-rose-900 dark:text-rose-200">
                        Perhatian Khusus
                    </span>
                </div>
                <p class="text-xs text-rose-800/90 dark:text-rose-300 leading-relaxed">
                    Lonjakan konsumsi dari 26,06 juta ton (2024) ke 31,10 juta ton (2025, +19,34%) dipicu oleh perluasan
                    cakupan metodologi.
                    Data 2024 bersumber dari Buku Statistik Konsumsi (konsumsi langsung rumah tangga), sedangkan
                    estimasi 2025 mencakup seluruh permintaan agregat (rumah tangga, industri olahan makanan, hotel,
                    restoran, dan katering).
                </p>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-kpi-card label="Konsumsi Total 2024" value="26,06" unit="juta ton" icon="bowl" color="amber"
                change="Buku Statistik Konsumsi 2024" change-type="neutral" />

            <x-kpi-card label="Konsumsi Total 2025" value="31,10" unit="juta ton" icon="bowl" color="amber"
                change="+19,34% YoY vs 2024" change-type="neutral"
                estimate="Rentang ketidakpastian: 30,00–31,00 juta ton; estimasi resmi ~31,10 juta ton." />

            <x-kpi-card label="Per Kapita 2024" value="92,40" unit="kg/thn" icon="scale" color="emerald"
                change="Basis Neraca Konsisten (Alt: 79,08)" change-type="positive" />

            <x-kpi-card label="Per Kapita 2025" value="91,58–92,37" unit="kg/thn" icon="scale" color="emerald"
                change="Midpoint: 91,98 kg/thn (−0,45% YoY)" change-type="neutral" />
        </div>

        {{-- Interactive Section: Uji Konsistensi Dua Basis Konsumsi Per Kapita 2024 --}}
        <div
            class="mt-8 rounded-2xl bg-white p-5 sm:p-6 ring-1 ring-slate-100 shadow-lg shadow-slate-200/60 dark:bg-slate-900 dark:ring-slate-800 dark:shadow-black/30">
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Uji Konsistensi Populasi: Dua Basis Per Kapita 2024</span>
                        <x-icon name="scale" class="size-4 text-emerald-600 dark:text-emerald-400" />
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Memverifikasi angka per kapita terhadap populasi Indonesia (~282–284 juta jiwa) dengan rumus:
                        <code
                            class="font-mono text-emerald-600 dark:text-emerald-400">Total Konsumsi (26,06 jt ton) ÷ Konsumsi Per Kapita</code>.
                    </p>
                </div>

                {{-- Basis Toggle Buttons --}}
                <div
                    class="flex items-center gap-1.5 p-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/60 shrink-0">
                    <button type="button" @click="perKapitaBasis = 'neraca'"
                        :class="perKapitaBasis === 'neraca' ? 'bg-emerald-600 text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
                        class="px-3 py-1.5 text-xs rounded-lg transition">
                        Basis Neraca (92,40 kg)
                    </button>
                    <button type="button" @click="perKapitaBasis = 'publikasi'"
                        :class="perKapitaBasis === 'publikasi' ? 'bg-amber-600 text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium'"
                        class="px-3 py-1.5 text-xs rounded-lg transition">
                        Basis Publikasi BPS (79,08 kg)
                    </button>
                </div>
            </div>

            {{-- Interactive Test Result Panel --}}
            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                <div
                    class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/60 dark:border-slate-800 dark:bg-slate-800/40">
                    <span
                        class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Konsumsi
                        Terpilih</span>
                    <span class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white"
                        x-text="perKapitaVal + ' kg/thn'">92,40 kg/thn</span>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                        x-text="perKapitaBasis === 'neraca' ? 'Basis acuan utama neraca pangan' : 'Angka susenas survei rumah tangga'">
                    </p>
                </div>

                <div
                    class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/60 dark:border-slate-800 dark:bg-slate-800/40">
                    <span
                        class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Implied
                        Population (Tersirat)</span>
                    <span class="mt-1 text-2xl font-extrabold"
                        :class="perKapitaBasis === 'neraca' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                        x-text="impliedPopulasi + ' juta jiwa'">282,03 juta jiwa</span>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                        x-text="perKapitaBasis === 'neraca' ? 'Deviasi hanya −0,03 jt jiwa vs populasi nyata 282 jt' : 'Overestimasi drastis +47,5 jt jiwa vs populasi nyata'">
                    </p>
                </div>

                <div class="p-4 rounded-xl border flex flex-col justify-between"
                    :class="perKapitaBasis === 'neraca' ? 'border-emerald-200 bg-emerald-50/50 dark:border-emerald-500/20 dark:bg-emerald-500/10' : 'border-rose-200 bg-rose-50/50 dark:border-rose-500/20 dark:bg-rose-500/10'">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider block"
                            :class="perKapitaBasis === 'neraca' ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300'">
                            Hasil Uji Konsistensi
                        </span>
                        <div class="mt-1 text-base font-extrabold"
                            :class="perKapitaBasis === 'neraca' ? 'text-emerald-800 dark:text-emerald-200' : 'text-rose-800 dark:text-rose-200'">
                            <span
                                x-text="perKapitaBasis === 'neraca' ? '✓ Cocok &amp; Valid' : '✗ Tidak Cocok (Distorsi)'"></span>
                        </div>
                    </div>
                    <p class="mt-2 text-xs leading-relaxed"
                        :class="perKapitaBasis === 'neraca' ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300'"
                        x-text="perKapitaBasis === 'neraca' ? 'Angka 92,4 kg/kapita/tahun secara matematis konsisten dengan total 26,06 juta ton.' : 'Angka 79,08 kg menghasilkan penduduk 330 juta jiwa, menunjukkan cakupan survei yang belum mencakup konsumsi luar rumah.'">
                    </p>
                </div>
            </div>
        </div>

        {{-- Primary Consumption Visualizations --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            {{-- Chart 1: RangeBar Ketidakpastian Konsumsi 2025 (ApexCharts) --}}
            <x-chart-card id="chart-consumption-range" title="Rentang Ketidakpastian Konsumsi 2025"
                subtitle="Membandingkan konsumsi 2024 (26,06 jt ton), rentang proyeksi 2025 (30,00–31,00 jt ton), dan estimasi resmi (31,10 jt ton)"
                badge="" source="BPS, Kementan &amp; Buletin Konsumsi Pangan"
                method-info="Grafik batang rentang horizontal (RangeBar) memvisualisasikan batas bawah, batas atas, dan titik estimasi resmi pemerintah."
                type="container" height="h-72 sm:h-80">
                <x-slot:table>
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr
                                class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                <th class="py-2 px-3 font-semibold">Tahun / Skenario</th>
                                <th class="py-2 px-3 font-semibold">Batas Min</th>
                                <th class="py-2 px-3 font-semibold">Batas Maks</th>
                                <th class="py-2 px-3 font-semibold">Titik Estimasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                            <tr>
                                <td class="py-2 px-3 font-sans">Realisasi 2024</td>
                                <td class="py-2 px-3">26,06 jt ton</td>
                                <td class="py-2 px-3">26,06 jt ton</td>
                                <td class="py-2 px-3 text-emerald-600 font-bold">26,06 jt ton</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans">Rentang Laporan 2025</td>
                                <td class="py-2 px-3">30,00 jt ton</td>
                                <td class="py-2 px-3">31,00 jt ton</td>
                                <td class="py-2 px-3 text-amber-600 font-bold">30,50 jt ton (Mid)</td>
                            </tr>
                            <tr class="bg-amber-500/10">
                                <td class="py-2 px-3 font-sans font-semibold text-amber-800 dark:text-amber-200">
                                    Estimasi Resmi Setkab 2025</td>
                                <td class="py-2 px-3">—</td>
                                <td class="py-2 px-3">—</td>
                                <td class="py-2 px-3 text-amber-800 dark:text-amber-200 font-extrabold">31,10 jt ton
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </x-slot:table>
            </x-chart-card>

            {{-- Chart 2: Uji Konsistensi Demografis 2025 (Chart.js) --}}
            <x-chart-card id="chart-population-consistency"
                title="Disparitas Konsumsi Rumah Tangga vs Total Nasional 2025"
                subtitle="Permintaan total resmi (31,10 jt ton) vs konsumsi implisit populasi 284 juta jiwa (26,01–26,23 jt ton)"
                badge="" source="Analisis RiceStatisticsService (Bagian A-C)"
                method-info="Konsumsi implisit dihitung dari per kapita 2025 (91,58–92,37 kg) dikalikan populasi resmi BPS ~284 juta jiwa."
                type="canvas" height="h-72 sm:h-80">
                <x-slot:table>
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr
                                class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                <th class="py-2 px-3 font-semibold">Kategori Perhitungan</th>
                                <th class="py-2 px-3 font-semibold">Volume Beras</th>
                                <th class="py-2 px-3 font-semibold">Selisih vs Resmi (31,10)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                            <tr class="bg-emerald-500/10">
                                <td class="py-2 px-3 font-sans font-semibold text-emerald-800 dark:text-emerald-200">
                                    Total Kebutuhan Resmi</td>
                                <td class="py-2 px-3 font-bold text-emerald-800 dark:text-emerald-200">31,10 juta ton
                                </td>
                                <td class="py-2 px-3">—</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans">Implisit RT Maks (92,37 kg × 284 jt)</td>
                                <td class="py-2 px-3">26,23 juta ton</td>
                                <td class="py-2 px-3 text-amber-600 font-semibold">−4,87 juta ton</td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-sans">Implisit RT Min (91,58 kg × 284 jt)</td>
                                <td class="py-2 px-3">26,01 juta ton</td>
                                <td class="py-2 px-3 text-rose-600 font-semibold">−5,09 juta ton</td>
                            </tr>
                        </tbody>
                    </table>
                </x-slot:table>
                <x-slot:footer>
                    <div class="text-[11px] leading-relaxed text-slate-500 dark:text-slate-400">
                        Diskrepansi ~4,9–5,1 juta ton mengindikasikan konsumsi komersial non-rumah tangga (Horeka,
                        industri makanan, pakan, &amp; benih).
                    </div>
                </x-slot:footer>
            </x-chart-card>
        </div>
    </div>
</x-layouts.app>