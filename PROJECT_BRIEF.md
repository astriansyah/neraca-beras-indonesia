<USER_REQUEST>
# PERAN & TUJUAN
Kamu adalah senior full-stack engineer + data visualization specialist. Bangun website dashboard "Neraca Beras Indonesia 2024â€“2025" menggunakan Laravel. Dashboard menampilkan SELURUH data pada dataset di bawah, lalu menambahkan analisis turunan berbasis metode statistika. Bekerja dalam mode Planning: buat rencana implementasi dulu, lalu eksekusi bertahap, dan di akhir verifikasi dengan menjalankan aplikasi serta memeriksa tiap halaman di browser.

# TECH STACK (WAJIB)
- Framework: Laravel 11/12 (PHP 8.2+), Blade + Vite. Database SQLite/MySQL via migration + seeder.
- CSS: Tailwind CSS (via Vite). Font Inter atau Plus Jakarta Sans.
- JS chart libraries (install via npm, bukan CDN): chart.js, apexcharts, d3 (+ d3-sankey).
- Tanpa SPA framework. Boleh pakai Alpine.js untuk interaksi ringan (tab, toggle, modal).

# DATASET (jadikan seeder; simpan sumber & catatan di tabel terpisah)
Tabel yang disarankan: categories, indicators (kode, nama, satuan, kategori), data_points (indicator_id, tahun, nilai, nilai_min, nilai_maks, is_estimate, catatan, sumber_url), breakdowns (indicator_id, tahun, label, nilai, satuan).

```json
{
  "produksi": {
    "gkg_juta_ton": {"2024": 53.14, "2025": 60.21},
    "rendemen_gkg_ke_beras_persen": {"2024": 57.62},
    "beras_juta_ton": {"2024": 30.62, "2025": 34.71},
    "luas_panen_juta_ha": {"2024": 10.05, "2025": 11.32},
    "sumber": {
      "2024": "https://www.bps.go.id/id/pressrelease/2025/02/03/2414/pada-2024-luas-panen-padi-mencapai-sekitar-10-05-juta-hektare-dengan-produksi-padi-sebanyak-53-14-juta-ton-gabah-kering-giling-gkg-.html",
      "2025": "https://www.bps.go.id/id/pressrelease/2026/02/02/2545/luas-panen-padi-pada-tahun-2025-mencapai-sekitar-11-32-juta-hektare-dengan-produksi-padi-sebanyak-60-21-juta-ton-gabah-kering-giling--gkg-.html"
    },
    "catatan": "2024: beras = GKG x 57,62% = 30,62 juta ton. 2025: beras 34,71 juta ton (setkab) sudah dalam bentuk beras."
  },
  "konsumsi": {
    "total_juta_ton": {"2024": 26.06, "2025": 31.1},
    "total_2025_rentang": {"min": 30, "maks": 31, "catatan": "30â€“31 juta ton/tahun; estimasi resmi ~31,1"},
    "perkapita_kg_tahun": {
      "2024_publikasi_statistik_konsumsi": 79.078,
      "2024_neraca_tertentu": 92.4,
      "2025_proyeksi_min": 91.58,
      "2025_proyeksi_maks": 92.37
    },
    "tren_perkapita_2024_vs_2023_persen": -2.26,
    "sumber": {
      "2024_total_dan_perkapita": "https://satudata.pertanian.go.id/assets/docs/publikasi/Buku_Statistik_Konsumsi_2024.pdf",
      "2025_total": "https://satudata.pertanian.go.id/details/publikasi/929",
      "2025_perkapita": "https://satudata.pertanian.go.id/assets/docs/publikasi/Buletin_Konsumsi_Pangan_Smtr1-2025-Ok.pdf"
    }
  },
  "impor": {
    "volume_juta_ton": {"2024": 4.52, "2025": 0.45073},
    "nilai_miliar_usd": {"2024": 2.71},
    "penurunan_2025_persen": 90,
    "negara_asal_2024_juta_ton": {"Thailand": 1.36, "Vietnam": 1.25, "Lainnya (Myanmar, Pakistan, India)": 1.91},
    "negara_asal_2025_ribu_ton": {"Myanmar": 206.44, "Lainnya": 244.29},
    "sumber": {
      "2024": "https://www.bps.go.id/id/statistics-table/1/MTA0MyMx/impor-beras-menurut-negara-asal-utama-2017-2023.html",
      "2025": "https://databoks.katadata.co.id/agroindustri/statistik/6ab1f24702002/volume-impor-beras-indonesia-berkurang-90-pada-2025"
    },
    "catatan": "'Lainnya' dihitung sebagai selisih total dikurangi negara yang disebutkan (turunan, bukan data sumber)."
  },
  "ekspor": {
    "2024": {"nilai_juta_usd": 3.86, "nilai_miliar_rp": 0.062, "volume_ton": 467.25},
    "2025": {"volume_kg_april_uji_pasar": 60, "surplus_produksi_juta_ton": 3.52, "total_produksi_nasional_juta_ton": 34.71,
             "catatan": "Impor konsumsi dihentikan; ekspor komersial 2025 sangat kecil; ekspor perdana skala besar awal 2026."},
    "sumber": {
      "2024": "https://www.kompas.id/artikel/mempertanyakan-ekspor-beras-premium",
      "2025": ["https://www.cnbcindonesia.com/news/20250602185212-4-637962/ri-mulai-ekspor-beras-april-2025-baru-60-kg", "https://setkab.go.id/indonesia-swasembada-pangan-2025-catat-sejarah/"]
    }
  },
  "stok_bulog_cbp_juta_ton": [
    {"periode": "Awal 2024", "min": 1.26, "maks": 1.4, "sumber": ["https://nasional.kontan.co.id/news/bulog-pastikan-stok-beras-aman-sampai-juni-2024", "https://infopublik.id/galeri/foto/detail/183496"]},
    {"periode": "Akhir 2024", "nilai": 2.0, "catatan": "Penyerapan domestik masif + sisa kuota impor", "sumber": ["https://infopublik.id/galeri/foto/detail/183496", "https://agroindonesia.co.id/akhir-2024-ri-punya-stok-beras-dua-juta-ton/"]},
    {"periode": "31 Des 2025", "nilai": 3.248472, "catatan": "Rekor serapan tertinggi sejak 1968; total kelolaan 4,35 juta ton", "sumber": ["https://www.cnbcindonesia.com/news/20260102104357-4-699103/serapan-beras-bulog-pecah-rekor-di-2025-stok-awal-2026-tembus-segini", "https://tirto.id/serapan-beras-bulog-32-juta-ton-di-2025-tertinggi-sejak-1968-hoCU"]}
  ]
}
```

# CATATAN KUALITAS DATA (tampilkan di halaman "Metodologi & Kualitas Data" dan sebagai badge peringatan di chart terkait)
1. Konsumsi per kapita 2024 punya dua basis: 79,078 vs 92,4 kg. Uji konsistensi: 26,06 juta ton Ã· 92,4 kg â‰ˆ 282 juta jiwa (cocok dengan populasi), sedangkan 79,078 kg tidak cocok. Gunakan 92,4 sebagai basis utama; 79,078 ditampilkan sebagai nilai alternatif.
2. Konsumsi total 2025 (31,1 juta ton) tidak konsisten dengan per kapita 2025 (91,58â€“92,37 kg Ã— ~284 juta jiwa â‰ˆ 26,0â€“26,2 juta ton). Lonjakan konsumsi +19% kemungkinan akibat perbedaan metodologi (cakupan permintaan total vs konsumsi rumah tangga). Beri badge "Perbedaan metodologi â€” hati-hati membandingkan antar tahun".
3. Stok Bulog adalah cadangan pemerintah saja, bukan stok nasional (belum termasuk stok pedagang, penggilingan, dan rumah tangga).
4. Surplus 2025 pada dokumen (3,52 juta ton) berbeda dari hitungan produksi âˆ’ konsumsi (34,71 âˆ’ 31,1 = 3,61). Tampilkan keduanya dan jelaskan selisihnya.
5. Hanya ada 2 titik waktu (2024 & 2025). Jangan membuat klaim signifikansi statistik, korelasi, atau regresi yang menyesatkan. Beri label "indikatif, n=2" pada setiap proyeksi atau tren.
6. Tandai data berupa rentang/estimasi (is_estimate = true) dengan ikon dan tooltip.

# ANALISIS STATISTIKA TAMBAHAN (hitung di Laravel Service class `RiceStatisticsService`, expose via JSON API, dan unit-test)
A. Statistik deskriptif & perubahan
- Perubahan absolut dan % YoY untuk semua indikator (produksi beras, GKG, luas panen, impor, konsumsi).
- Rata-rata, midpoint, dan rentang (minâ€“maks) untuk data berbentuk rentang (konsumsi 2025, per kapita 2025, stok awal 2024).
- Indikatif: gunakan CAGR/laju pertumbuhan 1 periode, bukan tren jangka panjang.

B. Dekomposisi pertumbuhan produksi
- Produktivitas = GKG Ã· luas panen (2024 â‰ˆ 5,29 ton/ha; 2025 â‰ˆ 5,32 ton/ha).
- Dekomposisi efek luas panen vs efek produktivitas terhadap kenaikan produksi GKG (metode log-decomposition atau selisih berurutan). Tampilkan kontribusi % masing-masing. Hasil diharapkan menunjukkan kenaikan didominasi efek luas panen.
- Analisis sensitivitas rendemen: produksi beras = GKG Ã— rendemen untuk rendemen 55%â€“62% (slider interaktif).

C. Neraca & indikator ketahanan pangan
- Neraca kotor = Produksi + Impor âˆ’ Ekspor âˆ’ Konsumsi (2024 dan 2025).
- Rasio Swasembada (SSR) = Produksi Ã· (Produksi + Impor âˆ’ Ekspor) Ã— 100.
- Import Dependency Ratio (IDR) = Impor Ã· (Produksi + Impor âˆ’ Ekspor) Ã— 100.
- Produksi Ã· Konsumsi (rasio kecukupan).
- Stock-to-use / Bulan cakupan stok = Stok Bulog Ã· (Konsumsi tahunan Ã· 12); hitung dengan dua skenario konsumsi (26,06 dan 31,1).
- Perubahan stok Bulog (Î”stok) per tahun dan residual tak terjelaskan = Neraca kotor âˆ’ Î”stok Bulog (stok non-Bulog, susut, penggunaan industri/pakan/benih). Beri tooltip penjelasan.
- Harga impor rata-rata 2024 = USD 2,71 miliar Ã· 4,52 juta ton â‰ˆ USD 600/ton. Harga unit ekspor 2024 = US$3,86 juta Ã· 467,25 ton â‰ˆ US$8.260/ton (tandai sebagai outlier karena kemungkinan beras premium/spesialti).

D. Konsentrasi pemasok impor
- Pangsa per negara, Herfindahlâ€“Hirschman Index (HHI) dan top-N concentration ratio untuk 2024 dan 2025. Pangsa Myanmar 2025 â‰ˆ 45,8%. Tandai bahwa 'Lainnya' adalah agregat, sehingga HHI adalah batas bawah.
- Analisis Pareto pemasok.

E. Ketidakpastian & skenario
- Simulasi Monte Carlo (10.000 iterasi, dijalankan di JS client-side atau di PHP) untuk surplus/neraca 2025 dengan distribusi triangular dari rentang data (konsumsi 30â€“31, rendemen 55â€“62%, dll.). Output: histogram, rata-rata, median, P5â€“P95, dan probabilitas surplus > 0. Tampilkan dengan D3.
- Skenario what-if interaktif: ubah konsumsi, rendemen, impor, dan stok awal, dan semua indikator serta chart ter-update real time.
- Peringatan eksplisit: ini simulasi berbasis rentang data resmi, bukan prediksi.

F. Opsional (jika waktu cukup)
- Proyeksi sederhana 2026 dengan 3 skenario (pesimis / dasar / optimis) memakai pertumbuhan indikatif, diberi label "Ilustratif, n=2".
- Z-score / indeks komposit "Skor Ketahanan Pangan Beras" dari SSR, IDR, bulan cakupan stok (minâ€“max normalization, bobot dapat diatur pengguna).

# HALAMAN & FITUR
1. Dashboard (Ringkasan): KPI cards (produksi, konsumsi, impor, stok, SSR) dengan YoY badge + sparkline ApexCharts; filter tahun 2024 / 2025 / Bandingkan; insight otomatis berupa teks naratif yang dihasilkan dari angka (mis. "Impor turun 90%â€¦").
2. Produksi: produksi beras & GKG, luas panen, produktivitas, dekomposisi pertumbuhan, sensitivitas rendemen.
3. Konsumsi: total, per kapita (dengan toggle basis 79,078 vs 92,4), rentang ketidakpastian 2025, uji konsistensi populasi.
4. Perdagangan Luar Negeri: impor (volume, nilai, per negara), ekspor, harga unit, HHI.
5. Stok Bulog: pergerakan stok, bulan cakupan, Î”stok.
6. Neraca & Aliran: diagram Sankey neraca per tahun, waterfall neraca, residual.
7. Simulasi & Skenario: Monte Carlo + what-if sliders.
8. Data & Sumber: tabel semua data_points dengan filter/search/sort, link sumber (buka tab baru), badge estimasi. Export CSV dan JSON; tombol "Unduh chart sebagai PNG" di setiap chart.
9. Metodologi & Kualitas Data: semua rumus (render dengan KaTeX atau teks rapi), asumsi, batasan, dan catatan kualitas data di atas.
10. Admin sederhana (CRUD data_points dengan Laravel validation, tanpa login dulu atau dengan Breeze minimal): saat data ditambah/diubah, semua statistik dan chart otomatis ter-update.

# PEMBAGIAN LIBRARY CHART
- Chart.js: bar grouped (produksi vs konsumsi vs impor per tahun), doughnut pangsa impor per negara, line/step stok Bulog, radar indikator ketahanan (2024 vs 2025), bar produktivitas.
- ApexCharts: radialBar (SSR & IDR), mixed bar+line (produksi + luas panen + produktivitas), rangeBar untuk data rentang & waterfall neraca, sparkline di KPI card, heatmap perbandingan indikator YoY.
- D3.js: Sankey aliran neraca (produksi + impor + stok awal â†’ konsumsi + ekspor + stok akhir + residual), treemap pangsa impor, histogram + kurva densitas Monte Carlo, bullet chart stok vs konsumsi bulanan, Pareto chart pemasok.
- Setiap chart dibungkus komponen Blade `<x-chart-card>` (judul, subtitle, sumber, tombol unduh, tombol info metode, state loading skeleton).

# DESAIN UI (Tailwind)
- Gaya modern, bersih, banyak whitespace. Palet utama hijau-emerald (padi) + aksen amber (gabah) + slate netral. Dukungan dark mode (class strategy) dan responsif penuh (mobile-first). Sidebar navigasi collapsible.
- Kartu: `rounded-2xl bg-white ring-1 ring-slate-100 shadow-lg shadow-slate-200/60 hover:shadow-xl transition`. Dark mode: `dark:bg-slate-900 dark:ring-slate-800 dark:shadow-black/30`.
- Chart BERBAYANGAN HALUS (wajib di ketiga library):
  - Chart.js: plugin custom `beforeDatasetDraw` yang mengatur `ctx.shadowColor = rgba(16,185,129,0.25)`, `shadowBlur = 12`, `shadowOffsetY = 6`, lalu reset di `afterDatasetDraw`. Bar dengan sudut membulat (`borderRadius: 10`), garis dengan tension halus.
  - ApexCharts: `chart.dropShadow = {enabled: true, top: 4, left: 0, blur: 8, opacity: 0.2}`, gradient fill, `stroke.curve: 'smooth'`.
  - D3: SVG `<filter>` dengan `feDropShadow` (stdDeviation â‰ˆ 4, flood-opacity â‰ˆ 0.2) pada bar/node/link; node Sankey sudut membulat dan link gradient.
- Animasi masuk halus (500â€“800 ms), tooltip kustom konsisten antar library, skema warna dan font sama di semua chart, format angka lokal Indonesia (`id-ID`: koma desimal, titik ribuan), satuan selalu tampil.
- Aksesibilitas: kontras cukup, aria-label pada chart, tabel alternatif di bawah tiap chart (toggle "Lihat tabel data").

# ARSITEKTUR LARAVEL
- Struktur: Models (Indicator, DataPoint, Breakdown, Category, Source), `RiceStatisticsService`, `DashboardController` dan API controller (`/api/stats/summary`, `/api/stats/balance`, `/api/stats/montecarlo`, dst.) dengan API Resources, FormRequest untuk validasi, Blade components, Vite entry per halaman (code-splitting agar bundle tiap halaman kecil).
- Seeder memuat dataset di atas secara lengkap dengan sumber URL. Semua angka tampil di UI HARUS berasal dari database/service, bukan hardcode di Blade/JS.
- Cache hasil statistik (Cache::remember) dan invalidasi saat data berubah.
- Tulis Pest/PHPUnit test untuk setiap rumus (SSR, IDR, dekomposisi, HHI, bulan cakupan, perubahan YoY) dengan nilai yang bisa diverifikasi manual dari dataset.
- README berisi cara instalasi: composer install, npm install, cp .env.example .env, php artisan key:generate, migrate --seed, npm run dev, php artisan serve.

# KRITERIA SELESAI (verifikasi sebelum menyatakan selesai)
- Semua angka dan sumber di dataset muncul di UI (cek halaman Data & Sumber).
- Setiap rumus di bagian "Analisis Statistika" punya tampilan chart/kartu + penjelasan di halaman Metodologi.
- Ketiga library (Chart.js, ApexCharts, D3) benar-benar dipakai dan semua chart bershadow halus.
- Semua catatan kualitas data tampil sebagai badge/peringatan.
- Tidak ada error di console browser; Lighthouse performance & accessibility â‰¥ 90 pada Dashboard; tampilan diuji di lebar 375px, 768px, dan 1440px (light & dark).
- Test PHP lulus. Sertakan screenshot tiap halaman sebagai artifact.
- Bahasa antarmuka: Bahasa Indonesia.
</USER_REQUEST>
<ADDITIONAL_METADATA>
The current local time is: 2026-10-05T13:49:06+07:00.

The user's current state is as follows:
Active Document: /Untitled-1 (LANGUAGE_UNSPECIFIED)
Cursor is on line: 1
</ADDITIONAL_METADATA>
<USER_SETTINGS_CHANGE>
The user changed setting `Model Selection` from None to Gemini 3.1 Pro (High). No need to comment on this change if the user doesn't ask about it. If reporting what model you are, please use a human readable name instead of the exact string.
</USER_SETTINGS_CHANGE>
