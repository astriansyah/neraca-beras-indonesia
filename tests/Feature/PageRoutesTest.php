<?php

namespace Tests\Feature;

use Tests\TestCase;

use PHPUnit\Framework\Attributes\DataProvider;

class PageRoutesTest extends TestCase
{
    #[DataProvider('pageRoutesProvider')]
    public function test_all_public_pages_return_successful_response(string $route, string $expectedContent): void
    {
        $response = $this->get($route);

        $response->assertStatus(200);
        $response->assertSee($expectedContent);
    }

    public static function pageRoutesProvider(): array
    {
        return [
            'Dashboard / Ringkasan' => ['/', 'Neraca Beras Indonesia'],
            'Produksi' => ['/produksi', 'Produksi Padi & Beras'],
            'Konsumsi' => ['/konsumsi', 'Konsumsi Beras'],
            'Perdagangan' => ['/perdagangan', 'Perdagangan Luar Negeri Beras'],
            'Stok Bulog' => ['/stok', 'Stok Cadangan Beras Pemerintah (Bulog)'],
            'Neraca & Aliran' => ['/neraca', 'Neraca & Aliran Beras'],
            'Simulasi & Skenario' => ['/simulasi', 'Simulasi Monte Carlo & Skenario What-if'],
            'Data & Sumber' => ['/data', 'Data & Sumber'],
            'Metodologi & Kualitas Data' => ['/metodologi', 'Metodologi & Kualitas Data'],
        ];
    }
}
