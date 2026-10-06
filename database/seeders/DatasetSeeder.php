<?php

namespace Database\Seeders;

use App\Models\Breakdown;
use App\Models\Category;
use App\Models\DataPoint;
use App\Models\Indicator;
use App\Models\Note;
use App\Models\Source;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Memuat SELURUH dataset Neraca Beras Indonesia 2024–2025 dari
 * database/data/neraca-beras.json (salinan verbatim dataset) ke tabel.
 *
 * Cara memperbarui data:
 *   1. Edit database/data/neraca-beras.json (dan metadata di seeder ini bila ada indikator baru)
 *   2. php artisan migrate:fresh --seed
 *   3. php artisan cache:clear   (seeder juga mem-flush cache statistik)
 */
class DatasetSeeder extends Seeder
{
    /** Metadata sumber: url => [key, judul, penerbit, tipe]. */
    private const SOURCE_META = [
        'bps.go.id/id/pressrelease/2025/02/03/2414' => ['bps-produksi-2024', 'Rilis BPS: Luas panen & produksi padi 2024', 'Badan Pusat Statistik', 'press-release'],
        'bps.go.id/id/pressrelease/2026/02/02/2545' => ['bps-produksi-2025', 'Rilis BPS: Luas panen & produksi padi 2025', 'Badan Pusat Statistik', 'press-release'],
        'Buku_Statistik_Konsumsi_2024.pdf' => ['kementan-statistik-konsumsi-2024', 'Buku Statistik Konsumsi Pangan 2024', 'Kementerian Pertanian (Satu Data)', 'pdf'],
        'details/publikasi/929' => ['kementan-publikasi-929', 'Publikasi Satu Data Pertanian No. 929 (konsumsi 2025)', 'Kementerian Pertanian (Satu Data)', 'web'],
        'Buletin_Konsumsi_Pangan_Smtr1-2025' => ['kementan-buletin-konsumsi-2025', 'Buletin Konsumsi Pangan Semester I 2025', 'Kementerian Pertanian (Satu Data)', 'pdf'],
        'statistics-table/1/MTA0MyMx' => ['bps-impor-negara-asal', 'Tabel BPS: Impor beras menurut negara asal utama', 'Badan Pusat Statistik', 'table'],
        'databoks.katadata.co.id' => ['databoks-impor-2025', 'Volume impor beras Indonesia berkurang 90% pada 2025', 'Databoks Katadata', 'web'],
        'kompas.id' => ['kompas-ekspor-2024', 'Mempertanyakan ekspor beras premium', 'Kompas.id', 'web'],
        'cnbcindonesia.com/news/20250602185212' => ['cnbc-ekspor-2025', 'RI mulai ekspor beras April 2025, baru 60 kg', 'CNBC Indonesia', 'web'],
        'setkab.go.id' => ['setkab-swasembada-2025', 'Indonesia swasembada pangan 2025 catat sejarah', 'Sekretariat Kabinet RI', 'web'],
        'kontan.co.id' => ['kontan-bulog-2024', 'Bulog pastikan stok beras aman sampai Juni 2024', 'Kontan', 'web'],
        'infopublik.id' => ['infopublik-bulog', 'Galeri InfoPublik: stok beras Bulog', 'InfoPublik (Kominfo)', 'web'],
        'agroindonesia.co.id' => ['agroindonesia-stok-2024', 'Akhir 2024 RI punya stok beras dua juta ton', 'Agro Indonesia', 'web'],
        'cnbcindonesia.com/news/20260102104357' => ['cnbc-bulog-2025', 'Serapan beras Bulog pecah rekor di 2025', 'CNBC Indonesia', 'web'],
        'tirto.id' => ['tirto-bulog-2025', 'Serapan beras Bulog 3,2 juta ton di 2025, tertinggi sejak 1968', 'Tirto.id', 'web'],
    ];

    /** @var array<string,int> url => source id */
    private array $sourceIds = [];

    /** @var array<string,Category> */
    private array $categories = [];

    public function run(): void
    {
        $path = database_path('data/neraca-beras.json');
        $d = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($d) {
            $this->seedCategories();
            $this->seedProduksi($d['produksi'], $d['ekspor']['sumber']['2025'][1]);
            $this->seedKonsumsi($d['konsumsi']);
            $this->seedImpor($d['impor']);
            $this->seedEkspor($d['ekspor']);
            $this->seedStok($d['stok_bulog_cbp_juta_ton']);
            $this->seedReferensi();
            $this->seedQualityNotes();
        });

        // Bersihkan cache hasil statistik agar data baru langsung terpakai.
        Cache::flush();
    }

    // ------------------------------------------------------------------ helpers

    private function source(string $url): int
    {
        if (isset($this->sourceIds[$url])) {
            return $this->sourceIds[$url];
        }
        foreach (self::SOURCE_META as $needle => [$key, $title, $publisher, $type]) {
            if (str_contains($url, $needle)) {
                $src = Source::create(compact('key', 'title', 'publisher', 'type') + ['url' => $url]);

                return $this->sourceIds[$url] = $src->id;
            }
        }
        throw new RuntimeException("Metadata sumber tidak ditemukan untuk URL: {$url}");
    }

    private function indicator(string $cat, string $code, string $name, string $unit, string $desc, int $sort, bool $ref = false): Indicator
    {
        return Indicator::create([
            'category_id' => $this->categories[$cat]->id,
            'code' => $code,
            'name' => $name,
            'unit' => $unit,
            'description' => $desc,
            'is_reference' => $ref,
            'sort' => $sort,
        ]);
    }

    /** @param string[] $urls */
    private function point(Indicator $ind, int $tahun, array $attrs, array $urls = []): DataPoint
    {
        $dp = $ind->dataPoints()->create($attrs + [
            'tahun' => $tahun,
            'sumber_url' => $urls[0] ?? null,
        ]);
        if ($urls) {
            $dp->sources()->sync(array_map(fn ($u) => $this->source($u), $urls));
        }

        return $dp;
    }

    /** @param string[] $urls */
    private function breakdown(Indicator $ind, int $tahun, array $attrs, array $urls): Breakdown
    {
        $b = $ind->breakdowns()->create($attrs + ['tahun' => $tahun, 'sumber_url' => $urls[0] ?? null]);
        $b->sources()->sync(array_map(fn ($u) => $this->source($u), $urls));

        return $b;
    }

    // ---------------------------------------------------------------- sections

    private function seedCategories(): void
    {
        $rows = [
            ['produksi', 'Produksi', 'Produksi padi (GKG), beras, luas panen, dan rendemen.'],
            ['konsumsi', 'Konsumsi', 'Konsumsi beras total dan per kapita.'],
            ['impor', 'Impor', 'Volume, nilai, dan negara asal impor beras.'],
            ['ekspor', 'Ekspor', 'Ekspor beras dan klaim surplus produksi.'],
            ['stok', 'Stok Bulog', 'Cadangan Beras Pemerintah (CBP) yang dikelola Perum Bulog.'],
            ['referensi', 'Referensi', 'Nilai acuan untuk uji konsistensi (bukan bagian dataset sumber).'],
        ];
        foreach ($rows as $i => [$slug, $name, $desc]) {
            $this->categories[$slug] = Category::create(['slug' => $slug, 'name' => $name, 'description' => $desc, 'sort' => $i]);
        }
    }

    private function seedProduksi(array $p, string $setkabUrl): void
    {
        $src = $p['sumber'];

        $gkg = $this->indicator('produksi', 'produksi_gkg', 'Produksi padi (GKG)', 'juta ton', 'Produksi padi dalam bentuk Gabah Kering Giling.', 1);
        foreach ($p['gkg_juta_ton'] as $y => $v) {
            $this->point($gkg, (int) $y, ['nilai' => $v], [$src[$y]]);
        }

        $rend = $this->indicator('produksi', 'rendemen', 'Rendemen GKG ke beras', '%', 'Faktor konversi GKG menjadi beras.', 2);
        foreach ($p['rendemen_gkg_ke_beras_persen'] as $y => $v) {
            $this->point($rend, (int) $y, ['nilai' => $v], [$src[$y]]);
        }

        $beras = $this->indicator('produksi', 'produksi_beras', 'Produksi beras', 'juta ton', 'Produksi beras untuk konsumsi pangan penduduk.', 3);
        foreach ($p['beras_juta_ton'] as $y => $v) {
            $note = $y === '2024'
                ? 'Beras = GKG × 57,62% = 30,62 juta ton.'
                : 'Angka Setkab; sudah dalam bentuk beras.';
            $urls = $y === '2025' ? [$src[$y], $setkabUrl] : [$src[$y]];
            $this->point($beras, (int) $y, ['nilai' => $v, 'catatan' => $note], $urls);
        }

        $luas = $this->indicator('produksi', 'luas_panen', 'Luas panen padi', 'juta ha', 'Luas panen padi nasional.', 4);
        foreach ($p['luas_panen_juta_ha'] as $y => $v) {
            $this->point($luas, (int) $y, ['nilai' => $v], [$src[$y]]);
        }

        Note::create([
            'code' => 'dataset-produksi', 'type' => 'dataset', 'category_id' => $this->categories['produksi']->id,
            'title' => 'Catatan dataset produksi', 'body' => $p['catatan'], 'severity' => 'info',
            'scopes' => ['production', 'data'],
        ]);
    }

    private function seedKonsumsi(array $k): void
    {
        $s = $k['sumber'];

        $tot = $this->indicator('konsumsi', 'konsumsi_total', 'Konsumsi beras total', 'juta ton', 'Total konsumsi/permintaan beras nasional per tahun.', 1);
        $this->point($tot, 2024, ['nilai' => $k['total_juta_ton']['2024']], [$s['2024_total_dan_perkapita']]);
        $r = $k['total_2025_rentang'];
        $this->point($tot, 2025, [
            'nilai' => $k['total_juta_ton']['2025'],
            'nilai_min' => $r['min'],
            'nilai_maks' => $r['maks'],
            'is_estimate' => true,
            'catatan' => $r['catatan'],
        ], [$s['2025_total']]);

        $pk = $k['perkapita_kg_tahun'];
        $main = $this->indicator('konsumsi', 'konsumsi_perkapita', 'Konsumsi per kapita (basis neraca)', 'kg/kapita/tahun', 'Basis utama: konsisten dengan populasi ±282 juta jiwa.', 2);
        $this->point($main, 2024, ['nilai' => $pk['2024_neraca_tertentu'], 'catatan' => 'Basis neraca tertentu (basis utama dashboard).'], [$s['2024_total_dan_perkapita']]);
        $this->point($main, 2025, [
            'nilai_min' => $pk['2025_proyeksi_min'],
            'nilai_maks' => $pk['2025_proyeksi_maks'],
            'is_estimate' => true,
            'catatan' => 'Proyeksi 2025 (rentang min–maks).',
        ], [$s['2025_perkapita']]);

        $alt = $this->indicator('konsumsi', 'konsumsi_perkapita_publikasi', 'Konsumsi per kapita (publikasi Statistik Konsumsi)', 'kg/kapita/tahun', 'Nilai alternatif; tidak konsisten dengan total konsumsi & populasi.', 3);
        $this->point($alt, 2024, ['nilai' => $pk['2024_publikasi_statistik_konsumsi'], 'catatan' => 'Basis publikasi Statistik Konsumsi (nilai alternatif).'], [$s['2024_total_dan_perkapita']]);

        $tr = $this->indicator('konsumsi', 'konsumsi_perkapita_tren', 'Tren konsumsi per kapita (YoY)', '%', 'Perubahan konsumsi per kapita 2024 dibanding 2023.', 4);
        $this->point($tr, 2024, ['nilai' => $k['tren_perkapita_2024_vs_2023_persen'], 'catatan' => '2024 dibanding 2023.'], [$s['2024_total_dan_perkapita']]);
    }

    private function seedImpor(array $m): void
    {
        $s = $m['sumber'];

        $vol = $this->indicator('impor', 'impor_volume', 'Volume impor beras', 'juta ton', 'Volume impor beras total.', 1);
        foreach ($m['volume_juta_ton'] as $y => $v) {
            $this->point($vol, (int) $y, ['nilai' => $v], [$s[$y]]);
        }

        $nil = $this->indicator('impor', 'impor_nilai', 'Nilai impor beras', 'miliar USD', 'Nilai impor beras (CIF).', 2);
        foreach ($m['nilai_miliar_usd'] as $y => $v) {
            $this->point($nil, (int) $y, ['nilai' => $v], [$s[$y]]);
        }

        $pen = $this->indicator('impor', 'impor_penurunan_dilaporkan', 'Penurunan impor (dilaporkan)', '%', 'Penurunan volume impor 2025 vs 2024 sebagaimana dilaporkan sumber.', 3);
        $this->point($pen, 2025, ['nilai' => $m['penurunan_2025_persen'], 'catatan' => 'Angka yang dilaporkan sumber (dibulatkan).'], [$s['2025']]);

        $i = 0;
        foreach ($m['negara_asal_2024_juta_ton'] as $label => $v) {
            $agg = str_starts_with($label, 'Lainnya');
            $this->breakdown($vol, 2024, [
                'label' => $label, 'nilai' => $v, 'satuan' => 'juta ton', 'is_aggregate' => $agg, 'is_derived' => $agg,
                'catatan' => $agg ? $m['catatan'] : null, 'sort' => $i++,
            ], [$s['2024']]);
        }
        $i = 0;
        foreach ($m['negara_asal_2025_ribu_ton'] as $label => $v) {
            $agg = str_starts_with($label, 'Lainnya');
            $this->breakdown($vol, 2025, [
                'label' => $label, 'nilai' => $v, 'satuan' => 'ribu ton', 'is_aggregate' => $agg, 'is_derived' => $agg,
                'catatan' => $agg ? $m['catatan'] : null, 'sort' => $i++,
            ], [$s['2025']]);
        }

        Note::create([
            'code' => 'dataset-impor', 'type' => 'dataset', 'category_id' => $this->categories['impor']->id,
            'title' => "Catatan dataset impor: 'Lainnya' adalah turunan", 'body' => $m['catatan'], 'severity' => 'info',
            'badge' => "'Lainnya' = selisih (turunan)", 'scopes' => ['trade', 'data'],
        ]);
    }

    private function seedEkspor(array $e): void
    {
        $s24 = $e['sumber']['2024'];
        [$cnbc, $setkab] = $e['sumber']['2025'];
        $e24 = $e['2024'];
        $e25 = $e['2025'];

        $usd = $this->indicator('ekspor', 'ekspor_nilai_usd', 'Nilai ekspor beras', 'juta USD', 'Nilai ekspor beras.', 1);
        $this->point($usd, 2024, ['nilai' => $e24['nilai_juta_usd']], [$s24]);

        $rp = $this->indicator('ekspor', 'ekspor_nilai_rp', 'Nilai ekspor beras (Rupiah)', 'miliar Rp', 'Nilai ekspor dalam Rupiah sebagaimana tercantum di dataset.', 2);
        $this->point($rp, 2024, ['nilai' => $e24['nilai_miliar_rp'], 'catatan' => 'Tercantum 0,062 "miliar Rp"; tidak konsisten dengan US$3,86 juta (≈ Rp 62 miliar). Kemungkinan satuan triliun Rp.'], [$s24]);

        $vol = $this->indicator('ekspor', 'ekspor_volume', 'Volume ekspor beras', 'ton', 'Volume ekspor beras.', 3);
        $this->point($vol, 2024, ['nilai' => $e24['volume_ton']], [$s24]);

        $uji = $this->indicator('ekspor', 'ekspor_uji_pasar', 'Ekspor uji pasar (April 2025)', 'kg', 'Ekspor beras uji pasar April 2025.', 4);
        $this->point($uji, 2025, ['nilai' => $e25['volume_kg_april_uji_pasar'], 'catatan' => 'Uji pasar April 2025; dipakai sebagai volume ekspor 2025 di neraca (0,00006 juta ton → praktis nol).'], [$cnbc]);

        $sur = $this->indicator('ekspor', 'surplus_produksi_dokumen', 'Surplus produksi (dokumen Setkab)', 'juta ton', 'Surplus produksi beras 2025 menurut dokumen pemerintah.', 5);
        $this->point($sur, 2025, ['nilai' => $e25['surplus_produksi_juta_ton'], 'catatan' => 'Berbeda dengan Produksi − Konsumsi (34,71 − 31,1 = 3,61).'], [$setkab]);

        $tot = $this->indicator('ekspor', 'produksi_nasional_dokumen', 'Total produksi nasional (dokumen Setkab)', 'juta ton', 'Total produksi beras nasional 2025 menurut dokumen Setkab.', 6);
        $this->point($tot, 2025, ['nilai' => $e25['total_produksi_nasional_juta_ton']], [$setkab]);

        Note::create([
            'code' => 'dataset-ekspor-2025', 'type' => 'dataset', 'category_id' => $this->categories['ekspor']->id,
            'title' => 'Catatan dataset ekspor 2025', 'body' => $e25['catatan'], 'severity' => 'info',
            'scopes' => ['trade', 'data'],
        ]);
    }

    private function seedStok(array $rows): void
    {
        $ind = $this->indicator('stok', 'stok_bulog_cbp', 'Stok Cadangan Beras Pemerintah (Bulog)', 'juta ton', 'Stok CBP di Perum Bulog — bukan stok nasional.', 1);
        foreach ($rows as $i => $r) {
            $tahun = (int) preg_replace('/\D/', '', substr($r['periode'], -4));
            $attrs = ['periode' => $r['periode'], 'catatan' => $r['catatan'] ?? null, 'sort' => $i];
            if (isset($r['nilai'])) {
                $attrs['nilai'] = $r['nilai'];
            } else {
                $attrs += ['nilai_min' => $r['min'], 'nilai_maks' => $r['maks'], 'is_estimate' => true];
                $attrs['catatan'] ??= 'Dilaporkan sebagai rentang (min–maks).';
            }
            $this->point($ind, $tahun, $attrs, $r['sumber']);
        }
    }

    private function seedReferensi(): void
    {
        $pop = $this->indicator('referensi', 'populasi_referensi', 'Populasi acuan (uji konsistensi)', 'juta jiwa', 'Asumsi populasi ~284 juta jiwa dari catatan kualitas data; bukan dataset sumber.', 1, true);
        $this->point($pop, 2025, ['nilai' => 284, 'is_estimate' => true, 'catatan' => 'Nilai acuan dari catatan kualitas data (~284 juta jiwa), bukan bagian dataset sumber.']);
    }

    private function seedQualityNotes(): void
    {
        $notes = [
            ['q1-perkapita-2024', 'Dua basis konsumsi per kapita 2024',
                'Konsumsi per kapita 2024 punya dua basis: 79,078 vs 92,4 kg. Uji konsistensi: 26,06 juta ton ÷ 92,4 kg ≈ 282 juta jiwa (cocok dengan populasi), sedangkan 79,078 kg menyiratkan ≈ 330 juta jiwa (tidak cocok). Dashboard memakai 92,4 sebagai basis utama; 79,078 ditampilkan sebagai nilai alternatif.',
                'warning', 'Dua basis per kapita', ['summary', 'consumption', 'data']],
            ['q2-metodologi-konsumsi-2025', 'Perbedaan metodologi konsumsi 2025',
                'Konsumsi total 2025 (31,1 juta ton) tidak konsisten dengan per kapita 2025 (91,58–92,37 kg × ~284 juta jiwa ≈ 26,0–26,2 juta ton). Lonjakan konsumsi +19% kemungkinan akibat perbedaan metodologi (cakupan permintaan total vs konsumsi rumah tangga). Hati-hati membandingkan antar tahun.',
                'danger', 'Perbedaan metodologi — hati-hati membandingkan antar tahun', ['summary', 'consumption', 'balance', 'simulation', 'stock', 'data']],
            ['q3-stok-bulog', 'Stok Bulog bukan stok nasional',
                'Stok Bulog adalah cadangan pemerintah (CBP) saja, bukan stok nasional — belum termasuk stok pedagang, penggilingan, dan rumah tangga.',
                'warning', 'Stok Bulog ≠ stok nasional', ['summary', 'stock', 'balance', 'simulation', 'data']],
            ['q4-surplus-2025', 'Selisih surplus 2025',
                'Surplus 2025 pada dokumen Setkab (3,52 juta ton) berbeda dari hitungan Produksi − Konsumsi (34,71 − 31,1 = 3,61 juta ton). Selisih 0,09 juta ton kemungkinan karena pembulatan atau basis konsumsi yang sedikit berbeda (3,52 menyiratkan konsumsi ≈ 31,19 juta ton). Keduanya ditampilkan.',
                'info', 'Surplus dokumen ≠ hitungan', ['summary', 'balance', 'trade', 'data']],
            ['q5-n2', 'Hanya dua titik waktu (n=2)',
                'Hanya ada 2 titik waktu (2024 & 2025). Tidak ada klaim signifikansi statistik, korelasi, atau regresi. Setiap proyeksi atau tren berlabel "indikatif, n=2" dan memakai laju pertumbuhan 1 periode.',
                'info', 'Indikatif, n=2', ['summary', 'production', 'consumption', 'trade', 'stock', 'balance', 'simulation']],
            ['q6-estimasi', 'Data rentang/estimasi',
                'Data berupa rentang atau estimasi (is_estimate = true) ditandai dengan ikon ≈ dan tooltip; analisis memakai midpoint dan menampilkan rentangnya.',
                'info', 'Mengandung estimasi/rentang', ['consumption', 'stock', 'simulation', 'data']],
            ['q7-hhi-batas', "HHI dan agregat 'Lainnya'",
                "Kategori 'Lainnya' adalah agregat beberapa negara. Menghitungnya sebagai satu entitas menghasilkan HHI batas ATAS (memecah agregat selalu menurunkan HHI); menganggapnya terfragmentasi penuh menghasilkan batas BAWAH. HHI sebenarnya berada di antara keduanya.",
                'warning', "'Lainnya' agregat — HHI berupa rentang", ['trade']],
            ['q8-ekspor-rupiah', 'Satuan nilai ekspor Rupiah 2024',
                'Nilai ekspor 2024 tercantum 0,062 "miliar Rp", padahal US$3,86 juta ≈ Rp 62 miliar (kurs ±Rp16.000). Kemungkinan satuan sebenarnya triliun Rp. Analisis harga unit memakai nilai USD.',
                'warning', 'Satuan Rupiah meragukan', ['trade', 'data']],
            ['q9-rentang-konsumsi-2025', 'Estimasi konsumsi 2025 di luar rentang',
                'Estimasi resmi konsumsi 2025 (31,1 juta ton) sedikit di atas rentang yang dilaporkan (30–31). Simulasi Monte Carlo memakai distribusi triangular min 30, modus 31,1, maks 31,1 agar estimasi resmi tercakup.',
                'info', 'Estimasi di luar rentang', ['consumption', 'simulation']],
            ['q10-ekspor-outlier', 'Harga unit ekspor 2024 outlier',
                'Harga unit ekspor 2024 ≈ US$8.261/ton, ±14× harga unit impor (≈ US$600/ton). Kemungkinan beras premium/spesialti dalam volume sangat kecil; bukan harga acuan.',
                'warning', 'Outlier — beras premium/spesialti', ['trade']],
        ];
        foreach ($notes as $i => [$code, $title, $body, $sev, $badge, $scopes]) {
            Note::create(compact('code', 'title', 'body', 'badge', 'scopes') + ['type' => 'quality', 'severity' => $sev, 'sort' => $i]);
        }
    }
}
