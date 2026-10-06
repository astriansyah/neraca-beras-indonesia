<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StatsApiTest extends TestCase
{
    #[DataProvider('apiEndpointsProvider')]
    public function test_api_endpoints_return_successful_json_with_warnings(string $endpoint, array $expectedKeys): void
    {
        $response = $this->getJson($endpoint);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
                'meta',
            ])
            ->assertJsonPath('status', 'success');

        foreach ($expectedKeys as $key) {
            $response->assertJsonStructure(['data' => [$key]]);
        }
    }

    public static function apiEndpointsProvider(): array
    {
        return [
            'Summary API' => ['/api/stats/summary', ['kpis', 'narrative', 'comparison']],
            'Production API' => ['/api/stats/production', ['gkg', 'luas_panen', 'produktivitas', 'dekomposisi_pertumbuhan', 'sensitivitas_rendemen']],
            'Consumption API' => ['/api/stats/consumption', ['total', 'perkapita', 'uji_konsistensi']],
            'Trade API' => ['/api/stats/trade', ['impor', 'ekspor', 'konsentrasi']],
            'Stock API' => ['/api/stats/stock', ['timeline', 'delta_stok', 'bulan_cakupan']],
            'Balance API' => ['/api/stats/balance', ['neraca_kotor', 'residual', 'indikator_ketahanan', 'sankey', 'waterfall']],
            'Simulation API' => ['/api/stats/simulation', ['parameters', 'defaults', 'iterations']],
            'Data Points API' => ['/api/data-points', []],
            'Sources API' => ['/api/sources', []],
        ];
    }

    public function test_data_points_can_be_filtered(): void
    {
        // Filter berdasarkan tahun 2024
        $resYear = $this->getJson('/api/data-points?tahun=2024');
        $resYear->assertStatus(200);
        $this->assertGreaterThan(0, $resYear->json('total'));

        // Filter berdasarkan kategori produksi
        $resCat = $this->getJson('/api/data-points?kategori=produksi');
        $resCat->assertStatus(200);
        $this->assertGreaterThan(0, $resCat->json('total'));

        // Pencarian teks
        $resSearch = $this->getJson('/api/data-points?q=GKG');
        $resSearch->assertStatus(200);
        $this->assertGreaterThan(0, $resSearch->json('total'));
    }

    public function test_sources_api_returns_all_sources(): void
    {
        $response = $this->getJson('/api/sources');

        $response->assertStatus(200);
        $this->assertEquals(15, $response->json('total'));
    }
}
