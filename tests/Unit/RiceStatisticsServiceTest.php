<?php

namespace Tests\Unit;

use App\Repositories\DatasetRepository;
use App\Services\RiceStatisticsService;
use Tests\TestCase;

class RiceStatisticsServiceTest extends TestCase
{
    protected RiceStatisticsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RiceStatisticsService(new DatasetRepository());
    }

    /**
     * Test Produktivitas: 2024 ≈ 5,29 ton/ha dan 2025 ≈ 5,32 ton/ha.
     */
    public function test_productivity_calculation(): void
    {
        $prod2024 = $this->service->calculateProductivity(53.14, 10.05);
        $prod2025 = $this->service->calculateProductivity(60.21, 11.32);

        $this->assertEqualsWithDelta(5.29, $prod2024, 0.01);
        $this->assertEqualsWithDelta(5.32, $prod2025, 0.01);
    }

    /**
     * Test Dekomposisi Logaritmik: Efek Luas ≈ 95%, Efek Produktivitas ≈ 5%.
     */
    public function test_log_decomposition(): void
    {
        $decomp = $this->service->calculateLogDecomposition(53.14, 60.21, 10.05, 11.32);

        $this->assertArrayHasKey('area_contribution_pct', $decomp);
        $this->assertArrayHasKey('productivity_contribution_pct', $decomp);

        // Luas berkontribusi ~95.25%, Produktivitas ~4.75%
        $this->assertEqualsWithDelta(95.25, $decomp['area_contribution_pct'], 0.5);
        $this->assertEqualsWithDelta(4.75, $decomp['productivity_contribution_pct'], 0.5);
        $this->assertEqualsWithDelta(100.0, $decomp['area_contribution_pct'] + $decomp['productivity_contribution_pct'], 0.01);
    }

    /**
     * Test Neraca Kotor: 2024 ≈ 9,08 juta ton, 2025 ≈ 4,06 juta ton.
     */
    public function test_gross_balance(): void
    {
        // 2024: 30.62 (prod) + 4.52 (impor) - 0.00046725 (ekspor) - 26.06 (kons) = 9.0795...
        $bal2024 = $this->service->calculateGrossBalance(30.62, 4.52, 467.25 / 1e6, 26.06);
        // 2025: 34.71 (prod) + 0.45073 (impor) - 0.00000006 (ekspor) - 31.10 (kons) = 4.0607...
        $bal2025 = $this->service->calculateGrossBalance(34.71, 0.45073, 60 / 1e9, 31.10);

        $this->assertEqualsWithDelta(9.08, $bal2024, 0.01);
        $this->assertEqualsWithDelta(4.06, $bal2025, 0.01);
    }

    /**
     * Test Rasio Swasembada (SSR) & Ketergantungan Impor (IDR).
     * 2024: SSR ≈ 87,1%, IDR ≈ 12,9%
     * 2025: SSR ≈ 98,7%, IDR ≈ 1,3%
     */
    public function test_ssr_and_idr(): void
    {
        $ssr2024 = $this->service->calculateSSR(30.62, 4.52, 467.25 / 1e6);
        $idr2024 = $this->service->calculateIDR(30.62, 4.52, 467.25 / 1e6);

        $this->assertEqualsWithDelta(87.1, $ssr2024, 0.1);
        $this->assertEqualsWithDelta(12.9, $idr2024, 0.1);
        $this->assertEqualsWithDelta(100.0, $ssr2024 + $idr2024, 0.01);

        $ssr2025 = $this->service->calculateSSR(34.71, 0.45073, 60 / 1e9);
        $idr2025 = $this->service->calculateIDR(34.71, 0.45073, 60 / 1e9);

        $this->assertEqualsWithDelta(98.7, $ssr2025, 0.1);
        $this->assertEqualsWithDelta(1.3, $idr2025, 0.1);
        $this->assertEqualsWithDelta(100.0, $ssr2025 + $idr2025, 0.01);
    }

    /**
     * Test Harga Unit Impor (≈ USD 600/ton) dan Ekspor (≈ USD 8.261/ton, outlier).
     */
    public function test_unit_prices(): void
    {
        // Impor 2024: USD 2.71 miliar / 4.52 juta ton
        $hargaImpor = (2.71 * 1e9) / (4.52 * 1e6);
        $this->assertEqualsWithDelta(599.56, $hargaImpor, 0.5);
        $this->assertEquals(600, round($hargaImpor));

        // Ekspor 2024: USD 3.86 juta / 467.25 ton
        $hargaEkspor = (3.86 * 1e6) / 467.25;
        $this->assertEqualsWithDelta(8261.1, $hargaEkspor, 0.5);
        $this->assertEquals(8261, round($hargaEkspor));
    }

    /**
     * Test Penurunan Impor YoY: ≈ −90,0%.
     */
    public function test_import_drop_percentage(): void
    {
        $yoy = $this->service->calculateYoY(4.52, 0.45073);
        $this->assertEqualsWithDelta(-90.0, $yoy['pct'], 0.1);
    }

    /**
     * Test Pangsa Pemasok Impor:
     * Thailand 2024 ≈ 30,1%, Vietnam 2024 ≈ 27,7%
     * Myanmar 2025 ≈ 45,8%
     */
    public function test_trade_supplier_shares(): void
    {
        $trade = $this->service->getTrade();

        // 2024
        $thaiShare = $trade['impor']['2024']['asal'][0]['share_pct'];
        $vietShare = $trade['impor']['2024']['asal'][1]['share_pct'];
        $this->assertEqualsWithDelta(30.1, $thaiShare, 0.1);
        $this->assertEqualsWithDelta(27.7, $vietShare, 0.1);

        // 2025
        $myanmarShare = $trade['impor']['2025']['asal'][0]['share_pct'];
        $this->assertEqualsWithDelta(45.8, $myanmarShare, 0.1);
    }

    /**
     * Test HHI dengan batas bawah dan atas.
     */
    public function test_hhi_calculation(): void
    {
        $hhi = $this->service->calculateHHI([30.09, 27.65, 42.26], 42.26);

        $this->assertArrayHasKey('hhi_upper', $hhi);
        $this->assertArrayHasKey('hhi_lower', $hhi);
        $this->assertGreaterThan($hhi['hhi_lower'], $hhi['hhi_upper']);
    }

    /**
     * Test Sensitivitas Rendemen GKG (55%–62%).
     */
    public function test_yield_sensitivity(): void
    {
        $sens = $this->service->calculateYieldSensitivity(60.21, 55.0, 62.0, 1.0);

        $this->assertCount(8, $sens);
        // Rendemen 55%: 60.21 * 0.55 = 33.12
        $this->assertEqualsWithDelta(33.12, $sens[0]['hasil_beras_juta_ton'], 0.05);
        // Rendemen 60%: 60.21 * 0.60 = 36.13
        $this->assertEqualsWithDelta(36.13, $sens[5]['hasil_beras_juta_ton'], 0.05);
    }

    /**
     * Test Rasio Kecukupan Produksi/Konsumsi:
     * 2024: 30.62 / 26.06 ≈ 1.175 (117,5%)
     * 2025: 34.71 / 31.10 ≈ 1.116 (111,6%)
     */
    public function test_adequacy_ratio(): void
    {
        $r2024 = $this->service->calculateAdequacyRatio(30.62, 26.06);
        $r2025 = $this->service->calculateAdequacyRatio(34.71, 31.10);

        $this->assertEqualsWithDelta(1.175, $r2024, 0.005);
        $this->assertEqualsWithDelta(1.116, $r2025, 0.005);
    }

    /**
     * Test Bulan Cakupan Stok Bulog.
     */
    public function test_stock_coverage_months(): void
    {
        // Stok 2.0 juta ton pada konsumsi 26.06 juta ton/tahun (2.1717 jt ton/bln)
        $m1 = $this->service->calculateStockCoverageMonths(2.0, 26.06);
        $this->assertEqualsWithDelta(0.92, $m1, 0.02);

        // Stok 3.248472 juta ton pada konsumsi 31.10 juta ton/tahun (2.5917 jt ton/bln)
        $m2 = $this->service->calculateStockCoverageMonths(3.248472, 31.10);
        $this->assertEqualsWithDelta(1.25, $m2, 0.02);
    }

    /**
     * Test Invalidation Cache saat DataPoint disimpan/diubah.
     */
    public function test_cache_invalidation_on_datapoint_change(): void
    {
        // Populate cache
        $this->service->getSummary();
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has('stats.summary'));

        // Trigger observer via touch()
        $point = \App\Models\DataPoint::first();
        $point->touch();

        // Verifikasi cache terhapus
        $this->assertFalse(\Illuminate\Support\Facades\Cache::has('stats.summary'));
    }
}
