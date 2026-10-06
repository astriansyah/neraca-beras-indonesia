<?php

namespace App\Services;

use App\Repositories\DatasetRepository;
use Illuminate\Support\Facades\Cache;

/**
 * Service pemrosesan data & statistik Neraca Beras Indonesia 2024–2025.
 * Menghitung seluruh rumus Bagian A–D dengan caching terintegrasi.
 */
class RiceStatisticsService
{
    public function __construct(
        protected DatasetRepository $repo
    ) {}

    // =========================================================================
    // API & HALAMAN: SUMMARY (Dashboard)
    // =========================================================================

    public function getSummary(): array
    {
        return Cache::remember('stats.summary', config('dashboard.cache_ttl'), function () {
            $prodGkg2024 = $this->repo->getDataPoint('produksi_gkg', 2024)?->nilai ?? 53.14;
            $prodGkg2025 = $this->repo->getDataPoint('produksi_gkg', 2025)?->nilai ?? 60.21;

            $prodBeras2024 = $this->repo->getDataPoint('produksi_beras', 2024)?->nilai ?? 30.62;
            $prodBeras2025 = $this->repo->getDataPoint('produksi_beras', 2025)?->nilai ?? 34.71;

            $luas2024 = $this->repo->getDataPoint('luas_panen', 2024)?->nilai ?? 10.05;
            $luas2025 = $this->repo->getDataPoint('luas_panen', 2025)?->nilai ?? 11.32;

            $konsTotal2024 = $this->repo->getDataPoint('konsumsi_total', 2024)?->nilai ?? 26.06;
            $konsTotal2025 = $this->repo->getDataPoint('konsumsi_total', 2025)?->nilai ?? 31.10;

            $imporVol2024 = $this->repo->getDataPoint('impor_volume', 2024)?->nilai ?? 4.52;
            $imporVol2025 = $this->repo->getDataPoint('impor_volume', 2025)?->nilai ?? 0.45073;

            // Ekspor dalam juta ton (2024: 467.25 ton = 0.00046725 jt ton; 2025: 60 kg = 0.00000006 jt ton)
            $eksporVol2024 = ($this->repo->getDataPoint('ekspor_volume', 2024)?->nilai ?? 467.25) / 1_000_000;
            $eksporVol2025 = ($this->repo->getDataPoint('ekspor_uji_pasar', 2025)?->nilai ?? 60) / 1_000_000_000;

            $stokAkhir2024 = $this->repo->getDataPoint('stok_bulog_cbp', 2024)?->nilai ?? 2.0;
            $stokAkhir2025 = $this->repo->getDataPoint('stok_bulog_cbp', 2025)?->nilai ?? 3.248472;

            // Perhitungan Indikator Kunci
            $prodYoY = $this->calculateYoY($prodBeras2024, $prodBeras2025);
            $gkgYoY = $this->calculateYoY($prodGkg2024, $prodGkg2025);
            $luasYoY = $this->calculateYoY($luas2024, $luas2025);
            $imporYoY = $this->calculateYoY($imporVol2024, $imporVol2025);
            $konsYoY = $this->calculateYoY($konsTotal2024, $konsTotal2025);
            $stokYoY = $this->calculateYoY($stokAkhir2024, $stokAkhir2025);

            $ssr2024 = $this->calculateSSR($prodBeras2024, $imporVol2024, $eksporVol2024);
            $ssr2025 = $this->calculateSSR($prodBeras2025, $imporVol2025, $eksporVol2025);
            $ssrYoY = $this->calculateYoY($ssr2024, $ssr2025);

            $idr2024 = $this->calculateIDR($prodBeras2024, $imporVol2024, $eksporVol2024);
            $idr2025 = $this->calculateIDR($prodBeras2025, $imporVol2025, $eksporVol2025);
            $idrYoY = $this->calculateYoY($idr2024, $idr2025);

            $neraca2024 = $this->calculateGrossBalance($prodBeras2024, $imporVol2024, $eksporVol2024, $konsTotal2024);
            $neraca2025 = $this->calculateGrossBalance($prodBeras2025, $imporVol2025, $eksporVol2025, $konsTotal2025);

            $prodTonHa2024 = $this->calculateProductivity($prodGkg2024, $luas2024);
            $prodTonHa2025 = $this->calculateProductivity($prodGkg2025, $luas2025);

            // Narasi Analisis Otomatis
            $narrative = [
                'headline' => 'Produksi Beras Nasional 2025 Melonjak +13,36% YoY; Impor Ditekan Hingga −90,03%',
                'points' => [
                    "Produksi beras nasional meningkat dari {$prodBeras2024} juta ton (2024) menjadi {$prodBeras2025} juta ton (2025), didorong oleh ekspansi luas panen dari {$luas2024} juta ha menjadi {$luas2025} juta ha (+12,64%).",
                    "Rasio Swasembada Beras (SSR) meningkat tajam dari ".\fmt_pct($ssr2024, 1)." menjadi ".\fmt_pct($ssr2025, 1).", seiring penurunan volume impor dari {$imporVol2024} juta ton menjadi ".\fmt_id($imporVol2025, 2)." juta ton.",
                    "Stok Cadangan Beras Pemerintah (CBP) di Bulog mencatatkan rekor tertinggi sejak 1968 sebesar ".\fmt_id($stokAkhir2025, 2)." juta ton per 31 Desember 2025.",
                    "Konsumsi resmi dilaporkan 31,10 juta ton (naik 19,34% YoY), namun catatan kualitas data menunjukkan adanya perbedaan metodologi antara konsumsi rumah tangga dan permintaan nasional total."
                ]
            ];

            return [
                'kpis' => [
                    'produksi_beras' => [
                        'label' => 'Produksi Beras',
                        'unit' => 'juta ton',
                        'val_2024' => $prodBeras2024,
                        'val_2025' => $prodBeras2025,
                        'yoy_abs' => $prodYoY['abs'],
                        'yoy_pct' => $prodYoY['pct'],
                        'sparkline' => [$prodBeras2024, $prodBeras2025],
                    ],
                    'konsumsi_total' => [
                        'label' => 'Konsumsi Beras',
                        'unit' => 'juta ton',
                        'val_2024' => $konsTotal2024,
                        'val_2025' => $konsTotal2025,
                        'range_2025' => ['min' => 30.0, 'maks' => 31.0],
                        'is_estimate' => true,
                        'yoy_abs' => $konsYoY['abs'],
                        'yoy_pct' => $konsYoY['pct'],
                        'sparkline' => [$konsTotal2024, $konsTotal2025],
                    ],
                    'impor_volume' => [
                        'label' => 'Volume Impor',
                        'unit' => 'juta ton',
                        'val_2024' => $imporVol2024,
                        'val_2025' => $imporVol2025,
                        'yoy_abs' => $imporYoY['abs'],
                        'yoy_pct' => $imporYoY['pct'],
                        'sparkline' => [$imporVol2024, $imporVol2025],
                    ],
                    'stok_bulog' => [
                        'label' => 'Stok CBP Bulog',
                        'unit' => 'juta ton',
                        'val_2024' => $stokAkhir2024,
                        'val_2025' => $stokAkhir2025,
                        'yoy_abs' => $stokYoY['abs'],
                        'yoy_pct' => $stokYoY['pct'],
                        'sparkline' => [$stokAkhir2024, $stokAkhir2025],
                    ],
                    'ssr' => [
                        'label' => 'Rasio Swasembada (SSR)',
                        'unit' => '%',
                        'val_2024' => round($ssr2024, 2),
                        'val_2025' => round($ssr2025, 2),
                        'yoy_abs' => round($ssrYoY['abs'], 2),
                        'yoy_pct' => round($ssrYoY['pct'], 2),
                        'sparkline' => [round($ssr2024, 2), round($ssr2025, 2)],
                    ],
                    'idr' => [
                        'label' => 'Ketergantungan Impor (IDR)',
                        'unit' => '%',
                        'val_2024' => round($idr2024, 2),
                        'val_2025' => round($idr2025, 2),
                        'yoy_abs' => round($idrYoY['abs'], 2),
                        'yoy_pct' => round($idrYoY['pct'], 2),
                        'sparkline' => [round($idr2024, 2), round($idr2025, 2)],
                    ],
                    'neraca_kotor' => [
                        'label' => 'Neraca Kotor',
                        'unit' => 'juta ton',
                        'val_2024' => round($neraca2024, 2),
                        'val_2025' => round($neraca2025, 2),
                        'sparkline' => [round($neraca2024, 2), round($neraca2025, 2)],
                    ],
                ],
                'narrative' => $narrative,
                'comparison' => [
                    'years' => [2024, 2025],
                    'metrics' => [
                        ['label' => 'Produksi GKG (jt ton)', '2024' => $prodGkg2024, '2025' => $prodGkg2025, 'yoy' => $gkgYoY['pct']],
                        ['label' => 'Produksi Beras (jt ton)', '2024' => $prodBeras2024, '2025' => $prodBeras2025, 'yoy' => $prodYoY['pct']],
                        ['label' => 'Luas Panen (jt ha)', '2024' => $luas2024, '2025' => $luas2025, 'yoy' => $luasYoY['pct']],
                        ['label' => 'Produktivitas Lahan (ton/ha)', '2024' => round($prodTonHa2024, 2), '2025' => round($prodTonHa2025, 2), 'yoy' => round((($prodTonHa2025 - $prodTonHa2024) / $prodTonHa2024) * 100, 2)],
                        ['label' => 'Konsumsi Beras (jt ton)', '2024' => $konsTotal2024, '2025' => $konsTotal2025, 'yoy' => $konsYoY['pct']],
                        ['label' => 'Impor Beras (jt ton)', '2024' => $imporVol2024, '2025' => round($imporVol2025, 4), 'yoy' => $imporYoY['pct']],
                        ['label' => 'Stok CBP Bulog Akhir (jt ton)', '2024' => $stokAkhir2024, '2025' => round($stokAkhir2025, 3), 'yoy' => $stokYoY['pct']],
                        ['label' => 'Rasio Swasembada (SSR %)', '2024' => round($ssr2024, 1), '2025' => round($ssr2025, 1), 'yoy' => round($ssrYoY['abs'], 1)],
                        ['label' => 'Rasio Impor (IDR %)', '2024' => round($idr2024, 1), '2025' => round($idr2025, 1), 'yoy' => round($idrYoY['abs'], 1)],
                    ]
                ],
                'warnings' => $this->repo->getNotesForScope('summary'),
            ];
        });
    }

    // =========================================================================
    // API & HALAMAN: PRODUKSI
    // =========================================================================

    public function getProduction(): array
    {
        return Cache::remember('stats.production', config('dashboard.cache_ttl'), function () {
            $gkg2024 = $this->repo->getDataPoint('produksi_gkg', 2024)?->nilai ?? 53.14;
            $gkg2025 = $this->repo->getDataPoint('produksi_gkg', 2025)?->nilai ?? 60.21;

            $luas2024 = $this->repo->getDataPoint('luas_panen', 2024)?->nilai ?? 10.05;
            $luas2025 = $this->repo->getDataPoint('luas_panen', 2025)?->nilai ?? 11.32;

            $beras2024 = $this->repo->getDataPoint('produksi_beras', 2024)?->nilai ?? 30.62;
            $beras2025 = $this->repo->getDataPoint('produksi_beras', 2025)?->nilai ?? 34.71;

            $rendemen2024 = $this->repo->getDataPoint('rendemen', 2024)?->nilai ?? 57.62;

            $prodTonHa2024 = $this->calculateProductivity($gkg2024, $luas2024);
            $prodTonHa2025 = $this->calculateProductivity($gkg2025, $luas2025);

            $decomp = $this->calculateLogDecomposition($gkg2024, $gkg2025, $luas2024, $luas2025);

            // Analisis Sensitivitas Rendemen (55% s.d 62%)
            $sens2024 = $this->calculateYieldSensitivity($gkg2024);
            $sens2025 = $this->calculateYieldSensitivity($gkg2025);

            return [
                'gkg' => [
                    '2024' => $gkg2024,
                    '2025' => $gkg2025,
                    'yoy' => $this->calculateYoY($gkg2024, $gkg2025),
                ],
                'luas_panen' => [
                    '2024' => $luas2024,
                    '2025' => $luas2025,
                    'yoy' => $this->calculateYoY($luas2024, $luas2025),
                ],
                'produktivitas' => [
                    '2024' => round($prodTonHa2024, 2),
                    '2024_exact' => $prodTonHa2024,
                    '2025' => round($prodTonHa2025, 2),
                    '2025_exact' => $prodTonHa2025,
                    'unit' => 'ton/ha',
                    'yoy' => $this->calculateYoY($prodTonHa2024, $prodTonHa2025),
                ],
                'beras' => [
                    '2024' => $beras2024,
                    '2025' => $beras2025,
                    'yoy' => $this->calculateYoY($beras2024, $beras2025),
                ],
                'rendemen' => [
                    '2024_resmi' => $rendemen2024,
                    '2025_implisit' => round(($beras2025 / $gkg2025) * 100, 2),
                ],
                'dekomposisi_pertumbuhan' => $decomp,
                'sensitivitas_rendemen' => [
                    'ranges' => array_column($sens2024, 'rendemen_pct'),
                    '2024' => $sens2024,
                    '2025' => $sens2025,
                ],
                'warnings' => $this->repo->getNotesForScope('production'),
            ];
        });
    }

    // =========================================================================
    // API & HALAMAN: KONSUMSI
    // =========================================================================

    public function getConsumption(): array
    {
        return Cache::remember('stats.consumption', config('dashboard.cache_ttl'), function () {
            $total2024 = $this->repo->getDataPoint('konsumsi_total', 2024)?->nilai ?? 26.06;
            $pt2025 = $this->repo->getDataPoint('konsumsi_total', 2025);
            $total2025 = $pt2025?->nilai ?? 31.10;
            $rangeMin2025 = $pt2025?->nilai_min ?? 30.0;
            $rangeMaks2025 = $pt2025?->nilai_maks ?? 31.0;
            $midpoint2025 = ($rangeMin2025 + $rangeMaks2025) / 2;

            $pkNeraca2024 = $this->repo->getDataPoint('konsumsi_perkapita', 2024)?->nilai ?? 92.40;
            $pkPub2024 = $this->repo->getDataPoint('konsumsi_perkapita_publikasi', 2024)?->nilai ?? 79.078;

            $ptPk2025 = $this->repo->getDataPoint('konsumsi_perkapita', 2025);
            $pkMin2025 = $ptPk2025?->nilai_min ?? 91.58;
            $pkMaks2025 = $ptPk2025?->nilai_maks ?? 92.37;
            $pkMidpoint2025 = ($pkMin2025 + $pkMaks2025) / 2;

            $popRef2025 = $this->repo->getDataPoint('populasi_referensi', 2025)?->nilai ?? 284.0;

            // Uji Konsistensi
            // 1. Uji konsistensi 2024: Total / Perkapita = Implied Population (juta jiwa)
            $impliedPopNeraca2024 = ($total2024 * 1_000_000_000) / ($pkNeraca2024 * 1_000_000); // 26.06e9 / (92.4 * 1e6) = 282.03
            $impliedPopPub2024 = ($total2024 * 1_000_000_000) / ($pkPub2024 * 1_000_000); // 329.55

            // 2. Uji konsistensi 2025: Perkapita * Populasi 284 juta jiwa = Implied Total (juta ton)
            $impliedTotalMin2025 = ($pkMin2025 * $popRef2025 * 1_000_000) / 1_000_000_000; // 26.01
            $impliedTotalMaks2025 = ($pkMaks2025 * $popRef2025 * 1_000_000) / 1_000_000_000; // 26.23

            return [
                'total' => [
                    '2024' => $total2024,
                    '2025_estimasi' => $total2025,
                    '2025_rentang' => [
                        'min' => $rangeMin2025,
                        'maks' => $rangeMaks2025,
                        'midpoint' => $midpoint2025,
                    ],
                    'yoy' => $this->calculateYoY($total2024, $total2025),
                ],
                'perkapita' => [
                    '2024' => [
                        'basis_neraca' => $pkNeraca2024,
                        'basis_publikasi' => $pkPub2024,
                        'selisih' => round($pkNeraca2024 - $pkPub2024, 3),
                    ],
                    '2025' => [
                        'min' => $pkMin2025,
                        'maks' => $pkMaks2025,
                        'midpoint' => round($pkMidpoint2025, 3),
                    ],
                    'tren_2024_vs_2023_persen' => $this->repo->getDataPoint('konsumsi_perkapita_tren', 2024)?->nilai ?? -2.26,
                ],
                'uji_konsistensi' => [
                    'populasi_acuan_2025' => $popRef2025,
                    'implied_populasi_2024_neraca' => round($impliedPopNeraca2024, 2),
                    'implied_populasi_2024_publikasi' => round($impliedPopPub2024, 2),
                    'implied_konsumsi_2025_dari_perkapita' => [
                        'min' => round($impliedTotalMin2025, 2),
                        'maks' => round($impliedTotalMaks2025, 2),
                    ],
                    'discrepancy_2025_juta_ton' => [
                        'vs_min' => round($total2025 - $impliedTotalMin2025, 2),
                        'vs_maks' => round($total2025 - $impliedTotalMaks2025, 2),
                    ],
                    'penjelasan' => 'Konsumsi 31,10 juta ton vs per kapita 91,58–92,37 kg (setara 26,0–26,2 jt ton) mencerminkan disparitas cakupan: 31,10 mencakup permintaan agregat (termasuk horeka & industri), sedangkan 92 kg adalah konsumsi langsung rumah tangga.'
                ],
                'warnings' => $this->repo->getNotesForScope('consumption'),
            ];
        });
    }

    // =========================================================================
    // API & HALAMAN: PERDAGANGAN (Trade)
    // =========================================================================

    public function getTrade(): array
    {
        return Cache::remember('stats.trade', config('dashboard.cache_ttl'), function () {
            $imporVol2024 = $this->repo->getDataPoint('impor_volume', 2024)?->nilai ?? 4.52;
            $imporVol2025 = $this->repo->getDataPoint('impor_volume', 2025)?->nilai ?? 0.45073;

            $imporNilai2024 = $this->repo->getDataPoint('impor_nilai', 2024)?->nilai ?? 2.71;

            $eksporUsd2024 = $this->repo->getDataPoint('ekspor_nilai_usd', 2024)?->nilai ?? 3.86;
            $eksporRp2024 = $this->repo->getDataPoint('ekspor_nilai_rp', 2024)?->nilai ?? 0.062;
            $eksporVolTon2024 = $this->repo->getDataPoint('ekspor_volume', 2024)?->nilai ?? 467.25;

            $eksporUjiKg2025 = $this->repo->getDataPoint('ekspor_uji_pasar', 2025)?->nilai ?? 60.0;
            $surplusDokumen2025 = $this->repo->getDataPoint('surplus_produksi_dokumen', 2025)?->nilai ?? 3.52;

            // Harga Unit
            $hargaImporPerTon2024 = ($imporNilai2024 * 1_000_000_000) / ($imporVol2024 * 1_000_000); // USD/ton
            $hargaEksporPerTon2024 = ($eksporUsd2024 * 1_000_000) / $eksporVolTon2024; // USD/ton

            // Breakdown Asal Impor
            $bd2024 = $this->repo->getBreakdowns('impor_volume', 2024);
            $origin2024 = [];
            foreach ($bd2024 as $b) {
                $origin2024[] = [
                    'label' => $b->label,
                    'volume_juta_ton' => $b->nilai,
                    'share_pct' => round(($b->nilai / $imporVol2024) * 100, 2),
                    'is_aggregate' => $b->is_aggregate,
                ];
            }

            $bd2025 = $this->repo->getBreakdowns('impor_volume', 2025);
            $origin2025 = [];
            $totalRibuTon2025 = $imporVol2025 * 1000; // 450.73 ribu ton
            foreach ($bd2025 as $b) {
                $origin2025[] = [
                    'label' => $b->label,
                    'volume_ribu_ton' => $b->nilai,
                    'volume_juta_ton' => round($b->nilai / 1000, 5),
                    'share_pct' => round(($b->nilai / $totalRibuTon2025) * 100, 2),
                    'is_aggregate' => $b->is_aggregate,
                ];
            }

            // Konsentrasi Pasar (HHI & Concentration Ratio)
            $hhi2024 = $this->calculateHHI(array_column($origin2024, 'share_pct'), 42.26);
            $hhi2025 = $this->calculateHHI(array_column($origin2025, 'share_pct'), 54.20);

            return [
                'impor' => [
                    '2024' => [
                        'volume_juta_ton' => $imporVol2024,
                        'nilai_miliar_usd' => $imporNilai2024,
                        'harga_unit_usd_per_ton' => round($hargaImporPerTon2024, 2),
                        'asal' => $origin2024,
                    ],
                    '2025' => [
                        'volume_juta_ton' => $imporVol2025,
                        'volume_ribu_ton' => $totalRibuTon2025,
                        'asal' => $origin2025,
                        'penurunan_persen' => round((($imporVol2025 - $imporVol2024) / $imporVol2024) * 100, 2),
                        'penurunan_dilaporkan' => 90.0,
                    ],
                ],
                'ekspor' => [
                    '2024' => [
                        'volume_ton' => $eksporVolTon2024,
                        'volume_juta_ton' => round($eksporVolTon2024 / 1_000_000, 8),
                        'nilai_juta_usd' => $eksporUsd2024,
                        'nilai_miliar_rp' => $eksporRp2024,
                        'harga_unit_usd_per_ton' => round($hargaEksporPerTon2024, 2),
                        'is_outlier' => true,
                        'catatan' => 'Harga ekspor ~US$8.261/ton (+14x harga impor), mengindikasikan beras premium/spesialti.',
                    ],
                    '2025' => [
                        'volume_uji_pasar_kg' => $eksporUjiKg2025,
                        'volume_juta_ton' => round($eksporUjiKg2025 / 1_000_000_000, 9),
                        'surplus_dokumen_juta_ton' => $surplusDokumen2025,
                        'catatan' => 'Ekspor komersial belum dibuka penuh pada 2025; hanya uji pasar 60 kg di April 2025.',
                    ],
                ],
                'konsentrasi' => [
                    '2024' => [
                        'cr1' => 30.09, // Thailand
                        'cr2' => 57.74, // Thailand + Vietnam
                        'hhi' => $hhi2024,
                    ],
                    '2025' => [
                        'cr1' => 45.80, // Myanmar
                        'hhi' => $hhi2025,
                    ],
                ],
                'warnings' => $this->repo->getNotesForScope('trade'),
            ];
        });
    }

    // =========================================================================
    // API & HALAMAN: STOK BULOG
    // =========================================================================

    public function getStock(): array
    {
        return Cache::remember('stats.stock', config('dashboard.cache_ttl'), function () {
            $konsTotal2024 = $this->repo->getDataPoint('konsumsi_total', 2024)?->nilai ?? 26.06;
            $konsTotal2025 = $this->repo->getDataPoint('konsumsi_total', 2025)?->nilai ?? 31.10;

            $pts = $this->repo->getIndicatorPoints('stok_bulog_cbp');

            $items = [];
            foreach ($pts as $p) {
                $items[] = [
                    'periode' => $p->periode,
                    'tahun' => $p->tahun,
                    'nilai' => $p->nilai,
                    'min' => $p->nilai_min,
                    'maks' => $p->nilai_maks,
                    'central' => $p->central(),
                    'is_estimate' => $p->is_estimate,
                    'catatan' => $p->catatan,
                ];
            }

            $stokAwal2024Mid = 1.33; // (1.26 + 1.40) / 2
            $stokAkhir2024 = 2.00;
            $stokAkhir2025 = 3.248472;

            // Perubahan Stok Bulog (Δstok)
            $deltaStok2024 = [
                'central' => round($stokAkhir2024 - $stokAwal2024Mid, 3),
                'min' => round($stokAkhir2024 - 1.40, 3), // 0.60
                'maks' => round($stokAkhir2024 - 1.26, 3), // 0.74
            ];
            $deltaStok2025 = [
                'central' => round($stokAkhir2025 - $stokAkhir2024, 6),
            ];

            // Bulan Cakupan (Stock Coverage in Months)
            $cakupan = [
                'skenario_26_06' => [
                    'konsumsi_tahunan' => $konsTotal2024,
                    'konsumsi_bulanan' => round($konsTotal2024 / 12, 4),
                    'awal_2024' => round($this->calculateStockCoverageMonths($stokAwal2024Mid, $konsTotal2024), 2),
                    'akhir_2024' => round($this->calculateStockCoverageMonths($stokAkhir2024, $konsTotal2024), 2),
                    'akhir_2025' => round($this->calculateStockCoverageMonths($stokAkhir2025, $konsTotal2024), 2),
                ],
                'skenario_31_10' => [
                    'konsumsi_tahunan' => $konsTotal2025,
                    'konsumsi_bulanan' => round($konsTotal2025 / 12, 4),
                    'awal_2024' => round($this->calculateStockCoverageMonths($stokAwal2024Mid, $konsTotal2025), 2),
                    'akhir_2024' => round($this->calculateStockCoverageMonths($stokAkhir2024, $konsTotal2025), 2),
                    'akhir_2025' => round($this->calculateStockCoverageMonths($stokAkhir2025, $konsTotal2025), 2),
                ],
            ];

            return [
                'timeline' => $items,
                'delta_stok' => [
                    '2024' => $deltaStok2024,
                    '2025' => $deltaStok2025,
                ],
                'bulan_cakupan' => $cakupan,
                'rekor_serapan_2025' => [
                    'stok_akhir' => $stokAkhir2025,
                    'total_kelolaan' => 4.35,
                    'catatan' => 'Rekor serapan beras Bulog tertinggi sejak 1968.',
                ],
                'warnings' => $this->repo->getNotesForScope('stock'),
            ];
        });
    }

    // =========================================================================
    // API & HALAMAN: NERACA & ALIRAN (Balance)
    // =========================================================================

    public function getBalance(): array
    {
        return Cache::remember('stats.balance', config('dashboard.cache_ttl'), function () {
            $prod2024 = $this->repo->getDataPoint('produksi_beras', 2024)?->nilai ?? 30.62;
            $prod2025 = $this->repo->getDataPoint('produksi_beras', 2025)?->nilai ?? 34.71;

            $impor2024 = $this->repo->getDataPoint('impor_volume', 2024)?->nilai ?? 4.52;
            $impor2025 = $this->repo->getDataPoint('impor_volume', 2025)?->nilai ?? 0.45073;

            $ekspor2024 = ($this->repo->getDataPoint('ekspor_volume', 2024)?->nilai ?? 467.25) / 1_000_000;
            $ekspor2025 = ($this->repo->getDataPoint('ekspor_uji_pasar', 2025)?->nilai ?? 60) / 1_000_000_000;

            $kons2024 = $this->repo->getDataPoint('konsumsi_total', 2024)?->nilai ?? 26.06;
            $kons2025 = $this->repo->getDataPoint('konsumsi_total', 2025)?->nilai ?? 31.10;

            $stokAwal2024 = 1.33; // midpoint
            $stokAkhir2024 = 2.00;
            $stokAkhir2025 = 3.248472;

            $deltaStok2024 = $stokAkhir2024 - $stokAwal2024; // 0.67
            $deltaStok2025 = $stokAkhir2025 - $stokAkhir2024; // 1.248472

            $neracaKotor2024 = $this->calculateGrossBalance($prod2024, $impor2024, $ekspor2024, $kons2024);
            $neracaKotor2025 = $this->calculateGrossBalance($prod2025, $impor2025, $ekspor2025, $kons2025);

            $residual2024 = $neracaKotor2024 - $deltaStok2024;
            $residual2025 = $neracaKotor2025 - $deltaStok2025;

            $ssr2024 = $this->calculateSSR($prod2024, $impor2024, $ekspor2024);
            $ssr2025 = $this->calculateSSR($prod2025, $impor2025, $ekspor2025);

            $idr2024 = $this->calculateIDR($prod2024, $impor2024, $ekspor2024);
            $idr2025 = $this->calculateIDR($prod2025, $impor2025, $ekspor2025);

            $rasioKecukupan2024 = $this->calculateAdequacyRatio($prod2024, $kons2024);
            $rasioKecukupan2025 = $this->calculateAdequacyRatio($prod2025, $kons2025);

            // Data Sankey Diagram untuk 2024 & 2025
            $sankey2024 = $this->buildSankeyData($prod2024, $impor2024, $stokAwal2024, $kons2024, $ekspor2024, $deltaStok2024, $residual2024);
            $sankey2025 = $this->buildSankeyData($prod2025, $impor2025, $stokAkhir2024, $kons2025, $ekspor2025, $deltaStok2025, $residual2025);

            // Data Waterfall Chart
            $waterfall2024 = [
                ['name' => 'Produksi Domestik', 'val' => $prod2024],
                ['name' => 'Impor Beras', 'val' => $impor2024],
                ['name' => 'Konsumsi Nasional', 'val' => -$kons2024],
                ['name' => 'Ekspor', 'val' => -round($ekspor2024, 4)],
                ['name' => 'Surplus Neraca Kotor', 'val' => round($neracaKotor2024, 2), 'is_summary' => true],
            ];
            $waterfall2025 = [
                ['name' => 'Produksi Domestik', 'val' => $prod2025],
                ['name' => 'Impor Beras', 'val' => round($impor2025, 4)],
                ['name' => 'Konsumsi Nasional', 'val' => -$kons2025],
                ['name' => 'Ekspor', 'val' => -round($ekspor2025, 6)],
                ['name' => 'Surplus Neraca Kotor', 'val' => round($neracaKotor2025, 2), 'is_summary' => true],
            ];

            return [
                'neraca_kotor' => [
                    '2024' => round($neracaKotor2024, 2),
                    '2024_exact' => $neracaKotor2024,
                    '2025' => round($neracaKotor2025, 2),
                    '2025_exact' => $neracaKotor2025,
                ],
                'residual' => [
                    '2024' => round($residual2024, 2),
                    '2025' => round($residual2025, 2),
                    'penjelasan' => 'Residual tak terjelaskan = Neraca Kotor − ΔStok Bulog. Menggambarkan akumulasi stok non-Bulog (pedagang, penggilingan, rumah tangga), susut pascapanen/rantai pasok, dan penggunaan industri/pakan/benih.'
                ],
                'indikator_ketahanan' => [
                    'ssr' => ['2024' => round($ssr2024, 2), '2025' => round($ssr2025, 2)],
                    'idr' => ['2024' => round($idr2024, 2), '2025' => round($idr2025, 2)],
                    'rasio_kecukupan' => ['2024' => round($rasioKecukupan2024, 3), '2025' => round($rasioKecukupan2025, 3)],
                    'surplus_selisih_2025' => [
                        'dokumen' => 3.52,
                        'hitungan' => round(34.71 - 31.10, 2), // 3.61
                        'selisih' => round(3.61 - 3.52, 2), // 0.09
                    ],
                ],
                'sankey' => [
                    '2024' => $sankey2024,
                    '2025' => $sankey2025,
                ],
                'waterfall' => [
                    '2024' => $waterfall2024,
                    '2025' => $waterfall2025,
                ],
                'warnings' => $this->repo->getNotesForScope('balance'),
            ];
        });
    }

    // =========================================================================
    // API & HALAMAN: SIMULASI (Simulation)
    // =========================================================================

    public function getSimulationParameters(): array
    {
        return Cache::remember('stats.simulation', config('dashboard.cache_ttl'), function () {
            return [
                'parameters' => [
                    'gkg_2025' => 60.21,
                    'rendemen' => [
                        'min' => 55.0,
                        'mode' => 57.65,
                        'max' => 62.0,
                        'unit' => '%',
                    ],
                    'konsumsi_2025' => [
                        'min' => 30.0,
                        'mode' => 31.1,
                        'max' => 31.1,
                        'unit' => 'juta ton',
                    ],
                    'impor_2025' => 0.45073,
                    'ekspor_2025' => 0.00006,
                    'stok_awal_2025' => 2.00,
                ],
                'defaults' => [
                    'rendemen' => 57.65,
                    'konsumsi' => 31.10,
                    'impor' => 0.45,
                    'stok_awal' => 2.00,
                ],
                'iterations' => 10_000,
                'warnings' => $this->repo->getNotesForScope('simulation'),
            ];
        });
    }

    // =========================================================================
    // METODE PERHITUNGAN MATEMATIS MURNI (Testable & Reusable)
    // =========================================================================

    /**
     * Hitung perubahan absolut dan persentase YoY.
     *
     * @return array{abs: float, pct: float}
     */
    public function calculateYoY(float $y1, float $y2): array
    {
        $abs = $y2 - $y1;
        $pct = $y1 != 0.0 ? ($abs / $y1) * 100 : 0.0;

        return [
            'abs' => round($abs, 4),
            'pct' => round($pct, 2),
        ];
    }

    /**
     * Produktivitas lahan (ton/ha) = GKG / Luas Panen.
     */
    public function calculateProductivity(float $gkgJutaTon, float $luasJutaHa): float
    {
        return $luasJutaHa > 0 ? $gkgJutaTon / $luasJutaHa : 0.0;
    }

    /**
     * Dekomposisi logaritmik pertumbuhan produksi GKG:
     * ln(Y2/Y1) = ln(A2/A1) + ln(P2/P1)
     *
     * @return array<string, mixed>
     */
    public function calculateLogDecomposition(float $gkg1, float $gkg2, float $area1, float $area2): array
    {
        $prod1 = $this->calculateProductivity($gkg1, $area1);
        $prod2 = $this->calculateProductivity($gkg2, $area2);

        $lnY = log($gkg2 / $gkg1);
        $lnA = log($area2 / $area1);
        $lnP = log($prod2 / $prod1);

        $areaContribPct = $lnY != 0.0 ? ($lnA / $lnY) * 100 : 0.0;
        $prodContribPct = $lnY != 0.0 ? ($lnP / $lnY) * 100 : 0.0;

        return [
            'ln_delta_produksi' => round($lnY, 6),
            'ln_delta_luas' => round($lnA, 6),
            'ln_delta_produktivitas' => round($lnP, 6),
            'area_contribution_pct' => round($areaContribPct, 2),
            'productivity_contribution_pct' => round($prodContribPct, 2),
            'kesimpulan' => 'Kenaikan produksi GKG didominasi oleh efek luas panen (~'.round($areaContribPct, 0).'%) dibanding efek produktivitas (~'.round($prodContribPct, 0).'%).',
        ];
    }

    /**
     * Sensitivitas rendemen konversi GKG ke beras (55%–62%).
     *
     * @return array<int, array{rendemen_pct: float, rendemen_decimal: float, hasil_beras_juta_ton: float}>
     */
    public function calculateYieldSensitivity(float $gkg, float $minYield = 55.0, float $maxYield = 62.0, float $step = 0.5): array
    {
        $rows = [];
        for ($r = $minYield; $r <= $maxYield + 1e-6; $r += $step) {
            $rows[] = [
                'rendemen_pct' => round($r, 1),
                'rendemen_decimal' => round($r / 100, 4),
                'hasil_beras_juta_ton' => round($gkg * ($r / 100), 2),
            ];
        }

        return $rows;
    }

    /**
     * Neraca kotor = Produksi + Impor − Ekspor − Konsumsi
     */
    public function calculateGrossBalance(float $prod, float $impor, float $ekspor, float $konsumsi): float
    {
        return $prod + $impor - $ekspor - $konsumsi;
    }

    /**
     * Self-Sufficiency Ratio (SSR) = Produksi / (Produksi + Impor − Ekspor) × 100
     */
    public function calculateSSR(float $prod, float $impor, float $ekspor): float
    {
        $avail = $prod + $impor - $ekspor;

        return $avail > 0 ? ($prod / $avail) * 100 : 0.0;
    }

    /**
     * Import Dependency Ratio (IDR) = Impor / (Produksi + Impor − Ekspor) × 100
     */
    public function calculateIDR(float $prod, float $impor, float $ekspor): float
    {
        $avail = $prod + $impor - $ekspor;

        return $avail > 0 ? ($impor / $avail) * 100 : 0.0;
    }

    /**
     * Rasio Kecukupan Produksi terhadap Konsumsi = Produksi / Konsumsi
     */
    public function calculateAdequacyRatio(float $prod, float $konsumsi): float
    {
        return $konsumsi > 0 ? $prod / $konsumsi : 0.0;
    }

    /**
     * Bulan Cakupan Stok Bulog = Stok / (Konsumsi Tahunan / 12)
     */
    public function calculateStockCoverageMonths(float $stok, float $konsumsiTahunan): float
    {
        $monthly = $konsumsiTahunan / 12;

        return $monthly > 0 ? $stok / $monthly : 0.0;
    }

    /**
     * Herfindahl-Hirschman Index (HHI):
     * Sum of squared percentage shares.
     * Karena 'Lainnya' adalah agregat beberapa negara:
     * - Batas atas: 'Lainnya' dihitung sebagai 1 entitas tunggal.
     * - Batas bawah: 'Lainnya' dianggap terfragmentasi merata/infinitesimal.
     *
     * @param float[] $shares Persentase pangsa pasar (misal [30.09, 27.65, 42.26])
     */
    public function calculateHHI(array $shares, float $aggregateShare = 0.0): array
    {
        $sumSq = 0.0;
        foreach ($shares as $s) {
            $sumSq += ($s * $s);
        }

        $upper = round($sumSq, 1);
        // Batas bawah jika share agregat dipecah (mengurangi agregat^2)
        $lower = round($sumSq - ($aggregateShare * $aggregateShare), 1);

        return [
            'hhi_upper' => $upper,
            'hhi_lower' => $lower,
            'kategori_upper' => $upper < 1500 ? 'Kompetitif' : ($upper <= 2500 ? 'Terkonsentrasi Sedang' : 'Sangat Terkonsentrasi'),
            'catatan' => "'Lainnya' adalah agregat: HHI sebenarnya berada di antara batas bawah ({$lower}) dan batas atas ({$upper}).",
        ];
    }

    /**
     * Susun nodes dan links untuk D3 Sankey Diagram aliran neraca beras.
     */
    private function buildSankeyData(float $prod, float $impor, float $stokAwal, float $kons, float $ekspor, float $deltaStok, float $residual): array
    {
        $nodes = [
            ['name' => 'Produksi Domestik', 'category' => 'inflow'],
            ['name' => 'Impor Beras', 'category' => 'inflow'],
            ['name' => 'Pasokan Tersedia', 'category' => 'intermediate'],
            ['name' => 'Konsumsi Nasional', 'category' => 'outflow'],
            ['name' => 'Ekspor Beras', 'category' => 'outflow'],
            ['name' => 'Penambahan Stok Bulog', 'category' => 'stock'],
            ['name' => 'Residual / Stok Non-Bulog / Susut', 'category' => 'residual'],
        ];

        $links = [
            ['source' => 0, 'target' => 2, 'value' => round($prod, 2)],
            ['source' => 1, 'target' => 2, 'value' => max(round($impor, 3), 0.01)],
            ['source' => 2, 'target' => 3, 'value' => round($kons, 2)],
            ['source' => 2, 'target' => 4, 'value' => max(round($ekspor, 4), 0.001)],
            ['source' => 2, 'target' => 5, 'value' => max(round($deltaStok, 2), 0.01)],
            ['source' => 2, 'target' => 6, 'value' => max(round($residual, 2), 0.01)],
        ];

        return compact('nodes', 'links');
    }

    /**
     * Bersihkan seluruh cache statistik (dijalankan saat observer mendeteksi perubahan data).
     */
    public function clearCache(): void
    {
        $keys = [
            'stats.summary',
            'stats.production',
            'stats.consumption',
            'stats.trade',
            'stats.stock',
            'stats.balance',
            'stats.simulation',
            'footer.sources',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
