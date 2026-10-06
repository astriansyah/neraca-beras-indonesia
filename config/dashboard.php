<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi navigasi & halaman dashboard (publik, read-only)
|--------------------------------------------------------------------------
| key => [route name, label, ikon, entry Vite, deskripsi SEO]
*/

return [
    'pages' => [
        'dashboard' => [
            'route' => 'dashboard', 'label' => 'Ringkasan', 'icon' => 'home', 'entry' => 'resources/js/pages/dashboard.js',
            'title' => 'Neraca Beras Indonesia 2024–2025',
            'description' => 'Dashboard data interaktif neraca beras Indonesia 2024–2025: produksi, konsumsi, impor, ekspor, stok Bulog, dan indikator ketahanan pangan.',
        ],
        'production' => [
            'route' => 'production', 'label' => 'Produksi', 'icon' => 'sprout', 'entry' => 'resources/js/pages/production.js',
            'title' => 'Produksi Padi & Beras',
            'description' => 'Produksi GKG dan beras, luas panen, produktivitas, dekomposisi pertumbuhan, dan sensitivitas rendemen 2024–2025.',
        ],
        'consumption' => [
            'route' => 'consumption', 'label' => 'Konsumsi', 'icon' => 'bowl', 'entry' => 'resources/js/pages/consumption.js',
            'title' => 'Konsumsi Beras',
            'description' => 'Konsumsi beras total dan per kapita, rentang ketidakpastian 2025, dan uji konsistensi populasi.',
        ],
        'trade' => [
            'route' => 'trade', 'label' => 'Perdagangan Luar Negeri', 'icon' => 'ship', 'entry' => 'resources/js/pages/trade.js',
            'title' => 'Perdagangan Luar Negeri Beras',
            'description' => 'Impor dan ekspor beras: volume, nilai, negara asal, harga unit, dan konsentrasi pemasok (HHI).',
        ],
        'stock' => [
            'route' => 'stock', 'label' => 'Stok Bulog', 'icon' => 'warehouse', 'entry' => 'resources/js/pages/stock.js',
            'title' => 'Stok Cadangan Beras Pemerintah (Bulog)',
            'description' => 'Pergerakan stok CBP Bulog, bulan cakupan konsumsi, dan perubahan stok tahunan.',
        ],
        'balance' => [
            'route' => 'balance', 'label' => 'Neraca & Aliran', 'icon' => 'flow', 'entry' => 'resources/js/pages/balance.js',
            'title' => 'Neraca & Aliran Beras',
            'description' => 'Diagram Sankey dan waterfall neraca beras, residual tak terjelaskan, dan indikator ketahanan pangan.',
        ],
        'simulation' => [
            'route' => 'simulation', 'label' => 'Simulasi & Skenario', 'icon' => 'dice', 'entry' => 'resources/js/pages/simulation.js',
            'title' => 'Simulasi Monte Carlo & Skenario What-if',
            'description' => 'Simulasi Monte Carlo neraca beras 2025 dan skenario what-if interaktif berbasis rentang data resmi.',
        ],
        'data' => [
            'route' => 'data', 'label' => 'Data & Sumber', 'icon' => 'table', 'entry' => 'resources/js/pages/data.js',
            'title' => 'Data & Sumber',
            'description' => 'Tabel lengkap seluruh data neraca beras beserta sumber resmi, dapat dicari, difilter, dan diunduh (CSV/JSON).',
        ],
        'methodology' => [
            'route' => 'methodology', 'label' => 'Metodologi & Kualitas Data', 'icon' => 'book', 'entry' => 'resources/js/pages/methodology.js',
            'title' => 'Metodologi & Kualitas Data',
            'description' => 'Rumus, asumsi, batasan, dan catatan kualitas data yang dipakai dashboard neraca beras.',
        ],
    ],

    // Tahun yang tersedia di dataset (validasi query parameter API).
    'years' => [2024, 2025],

    // Masa simpan cache hasil statistik (detik). Cache juga di-flush oleh seeder.
    'cache_ttl' => 60 * 60 * 24,
];
