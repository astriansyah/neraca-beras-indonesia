<x-layouts.app :page="$page" :page-key="$pageKey">
    <div x-data="{
        rendemen: 57.65,
        gkg2024: 53.14,
        gkg2025: 60.21,
        get beras2024() {
            return (this.gkg2024 * (this.rendemen / 100)).toFixed(2);
        },
        get beras2025() {
            return (this.gkg2025 * (this.rendemen / 100)).toFixed(2);
        },
        get selisihResmi2025() {
            return (this.beras2025 - 34.71).toFixed(2);
        }
    }">
        <x-page-header eyebrow="Analisis Sektor Produksi" :title="$page['title']" :lead="$page['description']"
            :icon="$page['icon']" />

        {{-- KPI Cards --}}
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-kpi-card label="Produksi Padi GKG" value="60,21" unit="juta ton" icon="sprout" color="emerald"
                change="+13,30% YoY (2024: 53,14)" change-type="positive" />

            <x-kpi-card label="Luas Panen Padi" value="11,32" unit="juta ha" icon="scale" color="emerald"
                change="+12,64% YoY (2024: 10,05)" change-type="positive" />

            <x-kpi-card label="Produktivitas Lahan" value="5,32" unit="ton/ha" icon="chart-bar" color="amber"
                change="+0,59% YoY (2024: 5,29)" change-type="positive" />

            <x-kpi-card label="Produksi Beras" value="34,71" unit="juta ton" icon="bowl" color="emerald"
                change="+13,36% YoY (2024: 30,62)" change-type="positive" />
        </div>

        {{-- Primary Production Charts (Mixed Chart + Growth Decomposition) --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            {{-- Chart 1: Mixed Bar + Line (ApexCharts) --}}
            <div class="lg:col-span-2">
                <x-chart-card id="chart-production-mixed" title="Dinamika Produksi GKG, Luas Panen, &amp; Produktivitas"
                    subtitle="Membandingkan volume Gabah Kering Giling (GKG), ekspansi lahan, dan hasil panen per hektare (2024 vs 2025)"
                    badge="" source="BPS - Survei Kerangka Sampel Area (KSA) Padi 2024–2025"
                    source-url="https://www.bps.go.id"
                    method-info="Produktivitas dihitung dari Produksi GKG dibagi Luas Panen. Sumbu kiri menampilkan juta ton/ha, sumbu kanan menampilkan produktivitas ton/ha."
                    type="container" height="h-80 sm:h-96">
                    <x-slot:table>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                    <th class="py-2 px-3 font-semibold">Indikator</th>
                                    <th class="py-2 px-3 font-semibold">Tahun 2024</th>
                                    <th class="py-2 px-3 font-semibold">Tahun 2025</th>
                                    <th class="py-2 px-3 font-semibold">Perubahan YoY</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium text-slate-800 dark:text-slate-200">
                                        Produksi GKG</td>
                                    <td class="py-2 px-3">53,14 juta ton</td>
                                    <td class="py-2 px-3">60,21 juta ton</td>
                                    <td class="py-2 px-3 text-emerald-600 font-semibold">+13,30%</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium text-slate-800 dark:text-slate-200">Luas
                                        Panen</td>
                                    <td class="py-2 px-3">10,05 juta ha</td>
                                    <td class="py-2 px-3">11,32 juta ha</td>
                                    <td class="py-2 px-3 text-emerald-600 font-semibold">+12,64%</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium text-slate-800 dark:text-slate-200">
                                        Produktivitas</td>
                                    <td class="py-2 px-3">5,29 ton/ha</td>
                                    <td class="py-2 px-3">5,32 ton/ha</td>
                                    <td class="py-2 px-3 text-emerald-600 font-semibold">+0,59%</td>
                                </tr>
                            </tbody>
                        </table>
                    </x-slot:table>
                </x-chart-card>
            </div>

            {{-- Chart 2: Dekomposisi Logaritmik (Chart.js Doughnut) --}}
            <div>
                <x-chart-card id="chart-growth-decomp" title="Dekomposisi Kenaikan Produksi"
                    subtitle="Kontribusi relatif: Efek Luas Panen vs Efek Produktivitas" badge=""
                    source="Metode Dekomposisi Log: ln(Y2/Y1) = ln(A2/A1) + ln(P2/P1)"
                    method-info="Formula matematis memecah laju pertumbuhan output gabah menjadi kontribusi fraksional perubahan luas lahan dan produktivitas intensif."
                    type="canvas" height="h-80 sm:h-96">
                    <x-slot:table>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                    <th class="py-2 px-3 font-semibold">Komponen Dekomposisi</th>
                                    <th class="py-2 px-3 font-semibold">Nilai Log</th>
                                    <th class="py-2 px-3 font-semibold">Kontribusi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Efek Luas Panen</td>
                                    <td class="py-2 px-3">0,1190</td>
                                    <td class="py-2 px-3 text-emerald-600 font-bold">95,27%</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Efek Produktivitas</td>
                                    <td class="py-2 px-3">0,0059</td>
                                    <td class="py-2 px-3 text-amber-600 font-bold">4,73%</td>
                                </tr>
                                <tr class="font-bold">
                                    <td class="py-2 px-3 font-sans">Total Kenaikan GKG</td>
                                    <td class="py-2 px-3">0,1249</td>
                                    <td class="py-2 px-3">100,00%</td>
                                </tr>
                            </tbody>
                        </table>
                    </x-slot:table>
                    <x-slot:footer>
                        <div class="text-[11px] leading-relaxed text-slate-500 dark:text-slate-400 italic">
                            * Kesimpulan: Kenaikan produksi 2025 didominasi 95,3% oleh ekspansi luas lahan panen, bukan
                            intensifikasi produktivitas.
                        </div>
                    </x-slot:footer>
                </x-chart-card>
            </div>
        </div>

        {{-- Interactive Section: Sensitivitas Rendemen Beras (55%–62%) --}}
        <div class="mt-8">
            <x-chart-card id="chart-yield-sensitivity" title="Analisis Sensitivitas Rendemen GKG ke Beras"
                subtitle="Simulasi konversi produksi beras (juta ton) berdasarkan variasi tingkat rendemen giling (55,0% s.d. 62,0%)"
                badge="" source="BPS (Rendemen 2024 = 57,62%) &amp; Setkab (Rendemen Implisit 2025 = 57,65%)"
                method-info="Produksi Beras = GKG × Persentase Rendemen. Rendemen resmi BPS 2024 adalah 57,62%. Untuk 2025, angka beras 34,71 jt ton dari GKG 60,21 jt ton menyiratkan rendemen 57,65%."
                type="container" height="h-72 sm:h-80">
                {{-- Interactive Slider Controller inside chart card --}}
                <x-slot:controls>
                    <div
                        class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <label for="rendemen-slider"
                                    class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                    Simulasi Parameter Rendemen GKG ke Beras:
                                    <span class="text-emerald-600 dark:text-emerald-400 text-sm font-extrabold ml-1"
                                        x-text="rendemen + '%'">57,65%</span>
                                </label>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Geser slider untuk melihat dampak variasi rendemen terhadap total produksi beras
                                    siap konsumsi secara real-time.
                                </p>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <button type="button" @click="rendemen = 57.62"
                                    class="px-2.5 py-1 text-[11px] rounded-lg border border-slate-200 bg-white font-medium hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700">
                                    Standar BPS (57,62%)
                                </button>
                                <button type="button" @click="rendemen = 60.00"
                                    class="px-2.5 py-1 text-[11px] rounded-lg border border-slate-200 bg-white font-medium hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700">
                                    Target Modern (60,00%)
                                </button>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center gap-4">
                            <span class="text-xs font-mono font-semibold text-slate-400">55%</span>
                            <input id="rendemen-slider" type="range" min="55.0" max="62.0" step="0.1" x-model="rendemen"
                                @input="window.dispatchEvent(new CustomEvent('nb-rendemen-change', { detail: { value: parseFloat(rendemen) } }))"
                                class="w-full accent-emerald-500 h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer dark:bg-slate-700"
                                aria-label="Tingkat Rendemen">
                            <span class="text-xs font-mono font-semibold text-slate-400">62%</span>
                        </div>

                        {{-- Real-time Output Cards --}}
                        <div
                            class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 text-xs">
                            <div class="p-2 rounded-lg bg-white dark:bg-slate-800/80">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Hasil Beras
                                    2024</span>
                                <span class="font-extrabold text-slate-800 dark:text-slate-100"
                                    x-text="beras2024 + ' jt ton'">30,62 jt ton</span>
                            </div>
                            <div class="p-2 rounded-lg bg-white dark:bg-slate-800/80">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Hasil Beras
                                    2025</span>
                                <span class="font-extrabold text-emerald-600 dark:text-emerald-400"
                                    x-text="beras2025 + ' jt ton'">34,71 jt ton</span>
                            </div>
                            <div class="p-2 rounded-lg bg-white dark:bg-slate-800/80">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Deviasi vs Setkab
                                    (34,71)</span>
                                <span class="font-extrabold"
                                    :class="selisihResmi2025 >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                                    x-text="(selisihResmi2025 >= 0 ? '+' : '') + selisihResmi2025 + ' jt ton'">0,00 jt
                                    ton</span>
                            </div>
                            <div class="p-2 rounded-lg bg-white dark:bg-slate-800/80">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Pertumbuhan
                                    YoY</span>
                                <span class="font-extrabold text-emerald-600"
                                    x-text="(((beras2025 - beras2024)/beras2024)*100).toFixed(2) + '%'">+13,36%</span>
                            </div>
                        </div>
                    </div>
                </x-slot:controls>

                <x-slot:table>
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr
                                class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                <th class="py-1.5 px-3 font-semibold">Tingkat Rendemen</th>
                                <th class="py-1.5 px-3 font-semibold">Estimasi Beras 2024 (jt ton)</th>
                                <th class="py-1.5 px-3 font-semibold">Estimasi Beras 2025 (jt ton)</th>
                                <th class="py-1.5 px-3 font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                            <tr>
                                <td class="py-1.5 px-3 font-sans font-semibold">55,0% (Batas Bawah)</td>
                                <td class="py-1.5 px-3">29,23</td>
                                <td class="py-1.5 px-3">33,12</td>
                                <td class="py-1.5 px-3 font-sans text-slate-500">Penggilingan tradisional</td>
                            </tr>
                            <tr class="bg-amber-500/10">
                                <td class="py-1.5 px-3 font-sans font-semibold text-amber-700 dark:text-amber-300">
                                    57,62% (BPS 2024)</td>
                                <td class="py-1.5 px-3 font-bold text-amber-700 dark:text-amber-300">30,62</td>
                                <td class="py-1.5 px-3 font-bold text-amber-700 dark:text-amber-300">34,69</td>
                                <td class="py-1.5 px-3 font-sans text-amber-700 dark:text-amber-300">Angka konversi
                                    resmi BPS</td>
                            </tr>
                            <tr class="bg-emerald-500/10">
                                <td class="py-1.5 px-3 font-sans font-semibold text-emerald-700 dark:text-emerald-300">
                                    57,65% (Setkab 2025)</td>
                                <td class="py-1.5 px-3 font-bold text-emerald-700 dark:text-emerald-300">30,63</td>
                                <td class="py-1.5 px-3 font-bold text-emerald-700 dark:text-emerald-300">34,71</td>
                                <td class="py-1.5 px-3 font-sans text-emerald-700 dark:text-emerald-300">Rendemen
                                    implisit siaran pers Setkab</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-3 font-sans font-semibold">60,0% (Modern)</td>
                                <td class="py-1.5 px-3">31,88</td>
                                <td class="py-1.5 px-3">36,13</td>
                                <td class="py-1.5 px-3 font-sans text-slate-500">Modern rice milling plant</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-3 font-sans font-semibold">62,0% (Batas Atas)</td>
                                <td class="py-1.5 px-3">32,95</td>
                                <td class="py-1.5 px-3">37,33</td>
                                <td class="py-1.5 px-3 font-sans text-slate-500">Efisiensi maksimal teoritis</td>
                            </tr>
                        </tbody>
                    </table>
                </x-slot:table>
            </x-chart-card>
        </div>
    </div>
</x-layouts.app>