<x-layouts.app :page="$page" :page-key="$pageKey">
    <x-page-header
        eyebrow="Dokumentasi Ilmiah & Rigor Metodologis"
        :title="$page['title']"
        :lead="$page['description']"
        :icon="$page['icon']"
    />

    {{-- Banner Integritas Ilmiah --}}
    <div class="rounded-2xl border border-emerald-200/80 bg-gradient-to-r from-emerald-50/90 via-emerald-50/50 to-teal-50/40 p-4 shadow-xs dark:border-emerald-500/20 dark:from-emerald-950/30 dark:via-emerald-900/10 dark:to-teal-950/20">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 rounded-lg bg-emerald-500/10 p-1.5 text-emerald-600 dark:text-emerald-400">
                <x-icon name="check" class="size-5" />
            </div>
            <div class="space-y-1 text-xs text-emerald-950 dark:text-emerald-200">
                <p class="font-bold">Standar Transparansi &amp; Reproduksibilitas Analisis</p>
                <p class="leading-relaxed text-emerald-800/90 dark:text-emerald-300/80">
                    Seluruh perhitungan statistik pada dashboard ini dapat direproduksi (reproducible) berdasarkan formula matematika baku dan data rujukan publikasi resmi pemerintah Republik Indonesia. Tidak ada data yang diinterpolasi secara fiktif tanpa penjelasan metodologis.
                </p>
            </div>
        </div>
    </div>

    {{-- Seksi 1: Rumus-Rumus Matematika Formal (Render KaTeX) --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200/80 pb-3 dark:border-slate-800">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">1. Rumus &amp; Formulasi Matematika Statistik</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Dirender menggunakan KaTeX sesuai standar notasi matematika ilmiah internasional.</p>
            </div>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/20">
                8 Formulasi Baku
            </span>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            {{-- 1. SSR & IDR --}}
            <x-card>
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Self-Sufficiency &amp; Import Dependency</h3>
                    <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">FAO Standard</span>
                </div>
                <div class="my-4 rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-800/50">
                    <div class="math-render py-1" data-katex="\text{SSR} = \frac{\text{Produksi}}{\text{Produksi} + \text{Impor} - \text{Ekspor}} \times 100\%" data-display="block"></div>
                    <div class="math-render py-1 mt-2 border-t border-slate-200/60 dark:border-slate-700/60 pt-2" data-katex="\text{IDR} = \frac{\text{Impor}}{\text{Produksi} + \text{Impor} - \text{Ekspor}} \times 100\%" data-display="block"></div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    SSR mengukur persentase kebutuhan pangan yang dicukupi dari produksi domestik. IDR mengukur ketergantungan impor terhadap pasokan tersedia bersih. Nilai ambang swasembada tercapai jika <span class="math-render font-mono" data-katex="\text{SSR} \ge 100\%"></span>.
                </p>
            </x-card>

            {{-- 2. Dekomposisi Logaritmik --}}
            <x-card>
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Dekomposisi Logaritmik Pertumbuhan</h3>
                    <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">Produksi Padi</span>
                </div>
                <div class="my-4 rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-800/50">
                    <div class="math-render py-1" data-katex="\ln\left(\frac{Q_t}{Q_{t-1}}\right) = \ln\left(\frac{A_t}{A_{t-1}}\right) + \ln\left(\frac{Y_t}{Y_{t-1}}\right)" data-display="block"></div>
                    <div class="math-render py-1 mt-2 border-t border-slate-200/60 dark:border-slate-700/60 pt-2" data-katex="C_A = \frac{\ln(A_t / A_{t-1})}{\ln(Q_t / Q_{t-1})} \times 100\%, \quad C_Y = 100\% - C_A" data-display="block"></div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Memecah laju pertumbuhan produksi padi (<span class="math-render" data-katex="Q"></span>) menjadi kontribusi perubahan luas panen (<span class="math-render" data-katex="A"></span>) versus produktivitas per hektar (<span class="math-render" data-katex="Y"></span>). Pada tahun 2024, kontraksi luas panen menyumbang 95,3% dari total penurunan produksi padi nasional.
                </p>
            </x-card>

            {{-- 3. Produktivitas Lahan --}}
            <x-card>
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Produktivitas Lahan Padi (Yield)</h3>
                    <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">KSA BPS</span>
                </div>
                <div class="my-4 rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-800/50">
                    <div class="math-render py-2" data-katex="Y_t = \frac{Q_{\text{GKG}, t}}{A_{\text{panen}, t}} \quad (\text{ton GKG / hektar})" data-display="block"></div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Dihitung dari rasio total estimasi produksi Gabah Kering Giling (GKG) terhadap luas panen padi metode Kerangka Sampel Area (KSA). Produktivitas padi nasional tahun 2024 tercatat 5,27 ton GKG/ha, sedikit turun dari 5,29 ton GKG/ha pada 2023 (-0,38%).
                </p>
            </x-card>

            {{-- 4. Konversi GKG ke Beras (Rendemen) --}}
            <x-card>
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Konversi GKG ke Beras (Rendemen)</h3>
                    <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">Milling Recovery</span>
                </div>
                <div class="my-4 rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-800/50">
                    <div class="math-render py-2" data-katex="Q_{\text{beras}} = Q_{\text{GKG}} \times r, \quad r \in [55,0\%; 62,0\%]" data-display="block"></div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Rasio rendemen beras (<span class="math-render" data-katex="r"></span>) mengonversi gabah menjadi beras konsumsi. Angka konversi rujukan resmi BPS adalah <span class="math-render" data-katex="r_{\text{BPS}} = 57,65\%"></span>. Setiap fluktuasi 1% pada rendemen bernilai ekuivalen &plusmn;1,04 juta ton beras nasional.
                </p>
            </x-card>

            {{-- 5. HHI Rentang --}}
            <x-card>
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Konsentrasi Pasar Impor (HHI Rentang)</h3>
                    <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">Herfindahl-Hirschman</span>
                </div>
                <div class="my-4 rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-800/50">
                    <div class="math-render py-1" data-katex="\text{HHI}_{\text{lower}} = \sum_{i=1}^k s_i^2 \le \text{HHI} \le \sum_{i=1}^k s_i^2 + s_{\text{Lainnya}}^2 = \text{HHI}_{\text{upper}}" data-display="block"></div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Penjumlahan kuadrat persentase pangsa pasar (<span class="math-render" data-katex="s_i"></span>). Karena BPS mengelompokkan sebagian negara ke dalam kategori agregat "Lainnya", HHI disajikan sebagai rentang analitis [3.447; 4.077], yang melampaui ambang pasar sangat terkonsentrasi KPPU (1.800) dan DOJ (2.500).
                </p>
            </x-card>

            {{-- 6. Bulan Cakupan Stok Bulog --}}
            <x-card>
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Bulan Cakupan Stok Cadangan Bulog</h3>
                    <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">Stock Coverage</span>
                </div>
                <div class="my-4 rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-800/50">
                    <div class="math-render py-2" data-katex="M_c = \frac{S_{\text{Bulog}}}{\frac{C_{\text{tahunan}}}{12}} \quad \text{vs Target FAO } M_{\text{FAO}} = 3,0 \text{ bulan}" data-display="block"></div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Mengukur ketahanan stok fisik beras CBP Bulog terhadap laju konsumsi bulanan. Dengan stok akhir 2024 sebesar 2,00 juta ton, cakupan stok berada pada kisaran 0,77 bulan (basis Bapanas 31,10 jt ton) hingga 0,92 bulan (basis BPS 26,06 jt ton).
                </p>
            </x-card>

            {{-- 7. Neraca Kotor & Dekomposisi Residual --}}
            <x-card>
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Neraca Kotor &amp; Residual Penyeimbang</h3>
                    <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">Mass Balance</span>
                </div>
                <div class="my-4 rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-800/50">
                    <div class="math-render py-1" data-katex="\text{Saldo} = \text{Produksi} + \text{Impor} - \text{Ekspor} - \text{Konsumsi}" data-display="block"></div>
                    <div class="math-render py-1 mt-2 border-t border-slate-200/60 dark:border-slate-700/60 pt-2" data-katex="\Delta S_{\text{Bulog}} + \text{Residual} = \text{Surplus Riil}" data-display="block"></div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Surplus riil 2024 (+2,93 juta ton) melebihi penambahan stok fisik Bulog (+0,70 juta ton), menyisakan residual tak terjelaskan sebesar +2,23 juta ton yang tersebar di rantai pasok swasta (penggilingan, distributor, rumah tangga, serta susut susut logistik).
                </p>
            </x-card>

            {{-- 8. Distribusi Triangular (Monte Carlo) --}}
            <x-card>
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-white">Distribusi Triangular Stokastik</h3>
                    <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">Monte Carlo</span>
                </div>
                <div class="my-4 rounded-xl bg-slate-50 p-4 text-center dark:bg-slate-800/50">
                    <div class="math-render py-2" data-katex="X \sim \text{Triangular}(a, c, b), \quad X = a + \sqrt{U(b-a)(c-a)} \text{ jika } U < \frac{c-a}{b-a}" data-display="block"></div>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Distribusi probabilitas kontinu berbentuk segitiga yang ditentukan oleh nilai minimum (<span class="math-render" data-katex="a"></span>), nilai mode paling mungkin (<span class="math-render" data-katex="c"></span>), dan nilai maksimum (<span class="math-render" data-katex="b"></span>). Digunakan untuk membangkitkan 10.000 skenario simulasi stokastik.
                </p>
            </x-card>
        </div>
    </div>

    {{-- Seksi 2: Dokumentasi 10 Catatan Integritas Kualitas Data --}}
    <div class="mt-8 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200/80 pb-3 dark:border-slate-800">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">2. Catatan Kritis Integritas &amp; Kualitas Data</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pengujian matematis terhadap potensi anomali, diskrepansi antar-instansi, dan peringatan batas interpretasi.</p>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/20">
                {{ count($notes) }} Catatan Resmi
            </span>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            @foreach($notes as $note)
                <div class="rounded-2xl border p-4 shadow-xs transition hover:shadow-sm
                    @if($note->severity === 'danger')
                        border-rose-200/80 bg-rose-50/40 dark:border-rose-500/20 dark:bg-rose-950/20
                    @elseif($note->severity === 'warning')
                        border-amber-200/80 bg-amber-50/40 dark:border-amber-500/20 dark:bg-amber-950/20
                    @else
                        border-slate-200/80 bg-slate-50/40 dark:border-slate-800 dark:bg-slate-900/40
                    @endif
                ">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2">
                            @if($note->severity === 'danger')
                                <span class="rounded-md bg-rose-500/10 p-1 text-rose-600 dark:text-rose-400">
                                    <x-icon name="alert-triangle" class="size-4" />
                                </span>
                            @elseif($note->severity === 'warning')
                                <span class="rounded-md bg-amber-500/10 p-1 text-amber-600 dark:text-amber-400">
                                    <x-icon name="alert-triangle" class="size-4" />
                                </span>
                            @else
                                <span class="rounded-md bg-sky-500/10 p-1 text-sky-600 dark:text-sky-400">
                                    <x-icon name="info" class="size-4" />
                                </span>
                            @endif
                            <h3 class="text-xs font-bold text-slate-900 dark:text-white">{{ $note->title }}</h3>
                        </div>

                        <span class="rounded-full px-2 py-0.5 text-[10px] font-extrabold uppercase font-mono
                            @if($note->severity === 'danger')
                                bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300
                            @elseif($note->severity === 'warning')
                                bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300
                            @else
                                bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300
                            @endif
                        ">
                            {{ $note->badge ?? $note->severity }}
                        </span>
                    </div>

                    <p class="mt-2 text-xs leading-relaxed text-slate-700 dark:text-slate-300">
                        {{ $note->body }}
                    </p>

                    @if(!empty($note->scopes))
                        <div class="mt-3 flex flex-wrap items-center gap-1.5 pt-2 border-t border-slate-200/50 dark:border-slate-800/60">
                            <span class="text-[10px] text-slate-400">Lingkup Halaman:</span>
                            @foreach($note->scopes as $sc)
                                <span class="rounded bg-white/80 px-1.5 py-0.5 text-[10px] font-medium text-slate-600 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700">
                                    {{ $sc }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Seksi 3: Batasan Rigor Statistik (Inferensi Ilmiah) --}}
    <x-card class="mt-8">
        <div class="border-b border-slate-100 pb-3 dark:border-slate-800">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">3. Prinsip Rigor Statistik &amp; Batasan Analisis</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Panduan etis dan ilmiah dalam membaca temuan analitik pada dashboard ini.</p>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-3 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-slate-800 dark:bg-slate-900/50">
                <span class="font-bold text-slate-900 dark:text-white block mb-1">Batasan Sampel Deret Waktu (n = 2)</span>
                Data yang dianalisis mencakup dua titik waktu (2024 dan 2025). Oleh karena itu, dashboard secara tegas tidak melakukan klaim korelasi sebab-akibat, regresi ekonometrika, atau proyeksi tren jangka panjang. Perbandingan disajikan secara indikatif untuk mengevaluasi dinamika transisi tahunan.
            </div>

            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-slate-800 dark:bg-slate-900/50">
                <span class="font-bold text-slate-900 dark:text-white block mb-1">Cakupan Stok CBP vs Stok Nasional</span>
                Stok Bulog yang dianalisis adalah Cadangan Beras Pemerintah (CBP). Bulog bertindak sebagai penyangga stabilisasi pasokan dan harga (SPHP), bukan pemilik seluruh stok beras di Indonesia. Sebagian besar stok riil berada di tangan masyarakat, pedagang pasar, dan penggilingan gabah.
            </div>

            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-slate-800 dark:bg-slate-900/50">
                <span class="font-bold text-slate-900 dark:text-white block mb-1">Status Angka Sementara Subround I 2025</span>
                Data produksi 2025 merupakan estimasi rilis KSA BPS Subround I (Januari–April 2025). Angka ini dapat berubah mengikuti realisasi luasan panen aktual pasca puncak panen raya dan dinamika iklim musim tanam kedua.
            </div>
        </div>
    </x-card>
</x-layouts.app>
