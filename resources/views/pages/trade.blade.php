<x-layouts.app :page="$page" :page-key="$pageKey">
    <script>window.__NB_TRADE__ = @json($stats);</script>
    <div x-data="{
        stats: window.__NB_TRADE__,
        viewYear: '2024',
    }">
        <x-page-header eyebrow="Perdagangan Internasional" :title="$page['title']" :lead="$page['description']"
            :icon="$page['icon']" />

        {{-- Warning Banner Outlier Ekspor & Penurunan Impor Ekstrem --}}
        <div
            class="mt-6 rounded-2xl border border-sky-500/20 bg-sky-50/50 p-4 sm:p-5 dark:border-sky-500/30 dark:bg-sky-950/20 flex flex-col sm:flex-row items-start gap-4">
            <span class="rounded-xl bg-sky-500/10 p-2.5 text-sky-600 dark:bg-sky-500/20 dark:text-sky-400 shrink-0">
                <x-icon name="alert" class="size-5" />
            </span>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-sm font-bold text-sky-900 dark:text-sky-200">
                        Catatan Pasar Internasional: Penurunan Impor −90,03% &amp; Anomali Unit Value Ekspor
                    </h2>
                    <span
                        class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-sky-200 text-sky-900 dark:bg-sky-900 dark:text-sky-200">
                        Indikatif
                    </span>
                </div>
                <p class="text-xs text-sky-800/90 dark:text-sky-300 leading-relaxed">
                    Volume impor beras nasional diproyeksikan anjlok 90,03% YoY dari 4,52 juta ton (2024) menjadi 0,45
                    juta ton (2025). Pada saat bersamaan, harga unit ekspor tercatat sebesar
                    <strong>US$8.261/ton</strong> (2024, volume 467 ton) dan <strong>US$1.200/ton</strong> (uji coba
                    pasar 2025, volume 60 kg), jauh melampaui harga rata-rata impor (~US$600/ton), membuktikan komoditas
                    ekspor adalah beras premium / spesialti organik berbobot volume kecil.
                </p>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-kpi-card label="Volume Impor 2024" value="4,52" unit="juta ton" icon="scale" color="sky"
                change="Nilai: USD 2,71 miliar" change-type="neutral" />

            <x-kpi-card label="Volume Impor 2025" value="0,45" unit="juta ton" icon="scale" color="emerald"
                change="−90,03% YoY (Kebutuhan darurat CBP)" change-type="positive" />

            <x-kpi-card label="Harga Rata-rata Impor" value="599,56" unit="USD/ton" icon="chart-bar" color="sky"
                change="Tahun 2024 (BPS)" change-type="neutral" />

            <x-kpi-card label="Ekspor Uji Coba 2025" value="60" unit="kg" icon="globe" color="amber"
                change="Harga: USD 1.200/ton (Beras Premium)" change-type="neutral"
                estimate="Hanya uji pasar terbatas April 2025; ekspor komersial belum dibuka penuh." />
        </div>

        {{-- Charts Row 1: Komposisi Negara Asal Impor & Diagram Pareto --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            {{-- Chart 1: Doughnut Chart.js Negara Asal Impor --}}
            <div>
                <x-chart-card id="chart-trade-origin-donut" title="Pangsa Negara Asal Impor 2024"
                    subtitle="Proporsi volume impor berdasarkan negara mitra dagang utama (BPS 2024)" badge=""
                    source="Badan Pusat Statistik (BPS) — Impor Beras Menurut Negara Asal 2024"
                    source-url="https://www.bps.go.id"
                    method-info="Pangsa pasar dihitung dari volume masing-masing negara dibagi total volume impor nasional (4,52 juta ton)."
                    type="canvas" height="h-72 sm:h-80">
                    <x-slot:table>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                    <th class="py-2 px-3 font-semibold">Negara Asal</th>
                                    <th class="py-2 px-3 font-semibold">Volume (jt ton)</th>
                                    <th class="py-2 px-3 font-semibold">Pangsa Pasar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Thailand</td>
                                    <td class="py-2 px-3">1,36</td>
                                    <td class="py-2 px-3 text-sky-600 font-bold">30,09%</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Vietnam</td>
                                    <td class="py-2 px-3">1,25</td>
                                    <td class="py-2 px-3 text-emerald-600 font-bold">27,65%</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Lainnya (Agregat)</td>
                                    <td class="py-2 px-3">1,91</td>
                                    <td class="py-2 px-3 text-slate-500 font-bold">42,26%</td>
                                </tr>
                            </tbody>
                        </table>
                    </x-slot:table>
                </x-chart-card>
            </div>

            {{-- Chart 2: ApexCharts Diagram Pareto Impor (Bar + Cumulative Line) --}}
            <div class="lg:col-span-2">
                <x-chart-card id="chart-trade-pareto" title="Diagram Pareto Pemasok Beras Internasional"
                    subtitle="Volume pasokan per kelompok negara dan garis konsentrasi kumulatif (%)" badge=""
                    source="Analisis Data Statistik Impor BPS 2024" source-url="https://www.bps.go.id"
                    method-info="Diagram Pareto memadukan volume impor (sumbu kiri) dan kurva persentase kumulatif (sumbu kanan) dengan ambang batas 80% ketergantungan pasokan."
                    type="container" height="h-72 sm:h-80">
                    <x-slot:table>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                    <th class="py-2 px-3 font-semibold">Pemasok</th>
                                    <th class="py-2 px-3 font-semibold">Volume (Juta Ton)</th>
                                    <th class="py-2 px-3 font-semibold">Pangsa Individual</th>
                                    <th class="py-2 px-3 font-semibold">Kumulatif (%)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Lainnya (Gabungan)</td>
                                    <td class="py-2 px-3">1,91</td>
                                    <td class="py-2 px-3">42,26%</td>
                                    <td class="py-2 px-3 font-bold">42,26%</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Thailand</td>
                                    <td class="py-2 px-3">1,36</td>
                                    <td class="py-2 px-3">30,09%</td>
                                    <td class="py-2 px-3 font-bold">72,35%</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Vietnam</td>
                                    <td class="py-2 px-3">1,25</td>
                                    <td class="py-2 px-3">27,65%</td>
                                    <td class="py-2 px-3 font-bold text-emerald-600">100,00%</td>
                                </tr>
                            </tbody>
                        </table>
                    </x-slot:table>
                </x-chart-card>
            </div>
        </div>

        {{-- Charts Row 2: Analisis Rentang HHI & Indeks Konsentrasi Pasar --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            {{-- Chart 3: ApexCharts Bar Rentang HHI --}}
            <div class="lg:col-span-2">
                <x-chart-card id="chart-trade-hhi" title="Indeks Konsentrasi Pasar Impor (Rentang HHI)"
                    subtitle="Herfindahl-Hirschman Index: Evaluasi risiko ketergantungan pemasok luar negeri" badge=""
                    source="Metodologi Bagian C (Rumus HHI Rentang)"
                    method-info="HHI = Σ(si²). Karena kategori 'Lainnya' (42,26%) merupakan agregat beberapa negara, HHI berada di antara batas bawah (jika Lainnya terpecah infinitesimal) dan batas atas (jika Lainnya adalah 1 entitas)."
                    type="container" height="h-72 sm:h-80">
                    <x-slot:table>
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                                    <th class="py-2 px-3 font-semibold">Tahun</th>
                                    <th class="py-2 px-3 font-semibold">HHI Batas Bawah</th>
                                    <th class="py-2 px-3 font-semibold">HHI Batas Atas</th>
                                    <th class="py-2 px-3 font-semibold">Klasifikasi Pasar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-mono">
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Tahun 2024</td>
                                    <td class="py-2 px-3">1.669,9</td>
                                    <td class="py-2 px-3 text-amber-600 font-bold">3.456,2</td>
                                    <td class="py-2 px-3 font-sans text-amber-600">Sangat Terkonsentrasi (Upper)</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-sans font-medium">Tahun 2025</td>
                                    <td class="py-2 px-3">2.482,4</td>
                                    <td class="py-2 px-3 text-rose-600 font-bold">5.420,0</td>
                                    <td class="py-2 px-3 font-sans text-rose-600">Oligopoli Ketat (Myanmar 45,8%)</td>
                                </tr>
                            </tbody>
                        </table>
                    </x-slot:table>
                </x-chart-card>
            </div>

            {{-- Explanatory Concentration Card --}}
            <div
                class="rounded-2xl bg-white p-5 sm:p-6 ring-1 ring-slate-100 shadow-lg shadow-slate-200/60 dark:bg-slate-900 dark:ring-slate-800 dark:shadow-black/30 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span
                            class="grid size-8 place-items-center rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400">
                            <x-icon name="scale" class="size-4" />
                        </span>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Standar Ambang Batas HHI</h3>
                    </div>

                    <div class="mt-4 space-y-3 text-xs leading-relaxed">
                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50/70 p-3 dark:border-slate-800 dark:bg-slate-800/50">
                            <div class="flex items-center justify-between font-bold text-slate-800 dark:text-slate-200">
                                <span>Pasar Kompetitif</span>
                                <span class="font-mono text-emerald-600 dark:text-emerald-400">HHI &lt; 1.500</span>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Banyak negara pemasok dengan
                                pangsa pasar merata.</p>
                        </div>

                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50/70 p-3 dark:border-slate-800 dark:bg-slate-800/50">
                            <div class="flex items-center justify-between font-bold text-slate-800 dark:text-slate-200">
                                <span>Konsentrasi Sedang</span>
                                <span class="font-mono text-amber-600 dark:text-amber-400">1.500 – 2.500</span>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Didominasi 3–4 pemasok utama;
                                risiko supply chain moderat.</p>
                        </div>

                        <div
                            class="rounded-xl border border-rose-200/80 bg-rose-50/60 p-3 dark:border-rose-900/40 dark:bg-rose-950/30">
                            <div class="flex items-center justify-between font-bold text-rose-800 dark:text-rose-300">
                                <span>Sangat Terkonsentrasi</span>
                                <span class="font-mono text-rose-600 dark:text-rose-400">HHI &gt; 2.500</span>
                            </div>
                            <p class="mt-1 text-[11px] text-rose-700/80 dark:text-rose-400">Rawan guncangan geopolitik
                                atau restriksi kuota ekspor negara asal.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400">
                    Sumber acuan ambang batas: US DOJ &amp; FTC Horizontal Merger Guidelines / Standar KPPU RI.
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>