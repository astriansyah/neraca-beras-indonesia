<x-layouts.app :page="$page" :page-key="$pageKey">
    <x-page-header
        eyebrow="Pusat Data Terbuka (Open Data)"
        :title="$page['title']"
        :lead="$page['description']"
        :icon="$page['icon']"
    />

    {{-- Ringkasan Metrik Basis Data --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Titik Data</p>
            <p class="mt-1 text-2xl font-extrabold tracking-tight text-emerald-600 dark:text-emerald-400 tabular-nums">{{ count($dataPoints) }} Indikator</p>
            <span class="mt-1 inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                Tersimpan di SQLite terstruktur
            </span>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Cakupan Kategori</p>
            <p class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white tabular-nums">5 Dimensi</p>
            <span class="mt-1 inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                Produksi, Konsumsi, Perdagangan, Stok, Neraca
            </span>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Rentang Waktu Analisis</p>
            <p class="mt-1 text-2xl font-extrabold tracking-tight text-sky-600 dark:text-sky-400 tabular-nums">2024 – 2025</p>
            <span class="mt-1 inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                Realisasi 2024 vs Proyeksi 2025
            </span>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Publikasi Sumber Resmi</p>
            <p class="mt-1 text-2xl font-extrabold tracking-tight text-indigo-600 dark:text-indigo-400 tabular-nums">{{ count($sources) }} Dokumen</p>
            <span class="mt-1 inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                BPS, Bapanas, Bulog, Setkab, BKF
            </span>
        </div>
    </div>

    {{-- Tabel Interaktif & Toolbar --}}
    <x-card>
        {{-- Toolbar Pencarian, Filter, dan Ekspor --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between border-b border-slate-100 pb-5 dark:border-slate-800">
            <div class="space-y-1">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Daftar Titik Data Statistik</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Gunakan pencarian dan pemfilteran untuk menelusuri data dan memeriksa referensi sumber.</p>
            </div>

            {{-- Action Buttons (Ekspor) --}}
            <div class="flex flex-wrap items-center gap-2">
                <button id="btn-export-csv" type="button" class="btn-ghost text-xs">
                    <x-icon name="download" class="size-3.5" /> Ekspor CSV
                </button>
                <button id="btn-export-json" type="button" class="btn-ghost text-xs">
                    <x-icon name="code" class="size-3.5" /> Ekspor JSON
                </button>
                <button id="btn-copy-table" type="button" class="btn-ghost text-xs" title="Salin seluruh tabel ke clipboard">
                    <x-icon name="clipboard" class="size-3.5" /> Salin Data
                </button>
            </div>
        </div>

        {{-- Filter Row --}}
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Search Bar --}}
            <div class="relative">
                <x-icon name="search" class="absolute left-3 top-2.5 size-4 text-slate-400" />
                <input
                    id="data-search-input"
                    type="text"
                    placeholder="Cari indikator atau sumber..."
                    class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-xs text-slate-900 placeholder-slate-400 focus:border-emerald-500 focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-white dark:placeholder-slate-500"
                />
            </div>

            {{-- Kategori Filter --}}
            <div>
                <select
                    id="filter-category"
                    class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-white"
                >
                    <option value="">Semua Kategori</option>
                    <option value="produksi">Produksi</option>
                    <option value="konsumsi">Konsumsi</option>
                    <option value="perdagangan">Perdagangan</option>
                    <option value="stok">Stok Bulog</option>
                    <option value="neraca">Neraca</option>
                </select>
            </div>

            {{-- Tahun Filter --}}
            <div>
                <select
                    id="filter-year"
                    class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-white"
                >
                    <option value="">Semua Tahun</option>
                    <option value="2024">2024 (Realisasi)</option>
                    <option value="2025">2025 (Estimasi / Target)</option>
                </select>
            </div>

            {{-- Status Filter --}}
            <div>
                <select
                    id="filter-status"
                    class="w-full rounded-xl border border-slate-200 bg-white py-2 px-3 text-xs text-slate-900 focus:border-emerald-500 focus:outline-none dark:border-slate-800 dark:bg-slate-950 dark:text-white"
                >
                    <option value="">Semua Status Data</option>
                    <option value="realisasi">Realisasi Resmi</option>
                    <option value="estimasi">Estimasi / Angka Sementara</option>
                </select>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="mt-4 overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-800/80">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Kategori</th>
                        <th>Indikator</th>
                        <th class="w-20 text-center">Tahun</th>
                        <th class="num">Nilai</th>
                        <th>Satuan</th>
                        <th>Status</th>
                        <th>Sumber Resmi</th>
                        <th class="w-16 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="data-table-body">
                    {{-- Diisi secara dinamis via JavaScript untuk filter, sort, dan pagination responsif --}}
                </tbody>
            </table>
        </div>

        {{-- Pagination & Count Footer --}}
        <div class="mt-4 flex flex-col items-center justify-between gap-3 sm:flex-row text-xs text-slate-500 dark:text-slate-400">
            <div id="data-counter">
                Menampilkan <strong class="text-slate-800 dark:text-slate-200" id="data-showing-count">0</strong> dari <strong class="text-slate-800 dark:text-slate-200" id="data-total-count">{{ count($dataPoints) }}</strong> indikator
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5">
                    <span>Per halaman:</span>
                    <select id="data-per-page" class="rounded-lg border border-slate-200 bg-white py-1 px-2 text-xs text-slate-800 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="all">Semua</option>
                    </select>
                </div>

                <div class="flex items-center gap-1">
                    <button id="btn-page-prev" type="button" class="icon-btn size-7" title="Halaman Sebelumnya">
                        <x-icon name="chevron-left" class="size-4" />
                    </button>
                    <span id="page-indicator" class="font-bold text-slate-800 dark:text-slate-200 px-2">1 / 1</span>
                    <button id="btn-page-next" type="button" class="icon-btn size-7" title="Halaman Berikutnya">
                        <x-icon name="chevron-right" class="size-4" />
                    </button>
                </div>
            </div>
        </div>
    </x-card>

    {{-- Katalog Dokumen Sumber Referensi Resmi --}}
    <x-card>
        <div class="border-b border-slate-100 pb-4 dark:border-slate-800">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Katalog Sumber Publikasi Resmi</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Daftar publikasi pemerintah yang menjadi rujukan dataset neraca beras nasional.</p>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($sources as $src)
                <div class="flex flex-col justify-between rounded-xl border border-slate-200/80 bg-slate-50/50 p-4 transition hover:border-emerald-500/40 hover:bg-emerald-50/20 dark:border-slate-800 dark:bg-slate-900/50 dark:hover:border-emerald-500/30">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/20">
                                {{ $src->publisher }}
                            </span>
                            <span class="text-[10px] text-slate-400 uppercase font-mono">{{ $src->type ?? 'Laporan' }}</span>
                        </div>
                        <h3 class="mt-2 text-xs font-bold text-slate-900 dark:text-white leading-snug">{{ $src->title }}</h3>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                        <span class="text-slate-500 dark:text-slate-400 font-mono text-[10px]">{{ $src->key }}</span>
                        @if($src->url)
                            <a href="{{ $src->url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">
                                Buka Publikasi <x-icon name="external-link" class="size-3" />
                            </a>
                        @else
                            <span class="text-slate-400">Dokumen Internal</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>

    {{-- Modal Detail Data Point --}}
    <div id="data-detail-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs opacity-0 pointer-events-none transition-opacity duration-200">
        <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900 transition-transform scale-95 duration-200" id="data-modal-card">
            <div class="flex items-start justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
                <div>
                    <span id="modal-category-badge" class="rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300">Produksi</span>
                    <h3 id="modal-indicator-title" class="mt-2 text-base font-bold text-slate-900 dark:text-white">Judul Indikator</h3>
                </div>
                <button id="btn-close-modal" type="button" class="icon-btn size-7 text-slate-400 hover:text-slate-700 dark:hover:text-white">
                    <x-icon name="x" class="size-4" />
                </button>
            </div>

            <div class="mt-4 space-y-3 text-xs">
                <div class="grid grid-cols-2 gap-2 rounded-xl bg-slate-50 p-3 dark:bg-slate-800/50">
                    <div>
                        <span class="text-slate-500 dark:text-slate-400 block text-[10px] uppercase font-bold">Tahun / Periode</span>
                        <span id="modal-year" class="font-extrabold text-slate-900 dark:text-white text-sm">2024</span>
                    </div>
                    <div>
                        <span class="text-slate-500 dark:text-slate-400 block text-[10px] uppercase font-bold">Nilai Tercatat</span>
                        <span id="modal-value" class="font-extrabold text-emerald-600 dark:text-emerald-400 text-sm font-mono">0,00</span>
                    </div>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block text-[10px] uppercase font-bold">Status &amp; Karakteristik Angka</span>
                    <p id="modal-status-desc" class="mt-0.5 text-slate-700 dark:text-slate-300 leading-relaxed">Angka realisasi resmi.</p>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block text-[10px] uppercase font-bold">Catatan Metodologi &amp; Kualitas Data</span>
                    <p id="modal-notes" class="mt-0.5 text-slate-700 dark:text-slate-300 leading-relaxed bg-amber-50/50 dark:bg-amber-950/20 p-2.5 rounded-lg border border-amber-200/50 dark:border-amber-500/20">Tidak ada catatan anomali.</p>
                </div>

                <div>
                    <span class="text-slate-500 dark:text-slate-400 block text-[10px] uppercase font-bold">Sumber Rujukan</span>
                    <div id="modal-sources-list" class="mt-1 space-y-1"></div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button id="btn-close-modal-footer" type="button" class="btn-primary text-xs px-4 py-2">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- Toast Notifikasi --}}
    <div id="data-toast" class="fixed bottom-6 right-6 z-50 flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white shadow-lg transition opacity-0 pointer-events-none dark:bg-white dark:text-slate-900">
        <x-icon name="check" class="size-4 text-emerald-400 dark:text-emerald-600" />
        <span id="toast-message">Data berhasil disalin!</span>
    </div>

    {{-- Oper data Points ke JavaScript --}}
    <script>
        window.__NB_DATA_POINTS__ = @json($dataPoints);
        window.__NB_SOURCES__ = @json($sources);
    </script>
</x-layouts.app>
