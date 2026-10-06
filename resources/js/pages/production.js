import { Chart, registerables } from 'chart.js';
import ApexCharts from 'apexcharts';
import {
    PALETTE,
    getThemeColors,
    formatNumber,
    chartJsShadowPlugin,
    getChartJsDefaults,
    getApexThemeDefaults,
} from '../charts/theme.js';

Chart.register(...registerables, chartJsShadowPlugin);

let mixedChart = null;
let decompChart = null;
let sensitivityChart = null;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initProductionCharts().catch(console.error));
} else {
    initProductionCharts().catch(console.error);
}

window.addEventListener('nb-theme-change', () => {
    updateProductionTheme();
});

window.addEventListener('nb-rendemen-change', (e) => {
    const val = e.detail?.value;
    if (sensitivityChart && val) {
        // Update annotation marker di sensitivity chart
        sensitivityChart.clearAnnotations();
        sensitivityChart.addPointAnnotation({
            x: `${val}%`,
            y: parseFloat((60.21 * (val / 100)).toFixed(2)),
            marker: {
                size: 7,
                fillColor: PALETTE.emerald,
                strokeColor: '#fff',
                strokeWidth: 2,
                radius: 4,
            },
            label: {
                borderColor: PALETTE.emerald,
                style: {
                    color: '#fff',
                    background: PALETTE.emerald,
                    fontFamily: "'Plus Jakarta Sans', sans-serif",
                    fontWeight: '700',
                    fontSize: '11px',
                },
                text: `${val}% → ${(60.21 * (val / 100)).toFixed(2)} jt ton`,
            }
        });
    }
});

async function initProductionCharts() {
    const c = getThemeColors();

    // =========================================================================
    // 1. APEXCHARTS: MIXED BAR + LINE (GKG, Luas Panen, Produktivitas)
    // =========================================================================
    const mixedEl = document.getElementById('chart-production-mixed');
    if (mixedEl) {
        const options = getApexThemeDefaults({
            chart: {
                type: 'line',
                height: 360,
                stacked: false,
            },
            stroke: {
                width: [0, 0, 3.5],
                curve: 'smooth',
            },
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    borderRadius: 6,
                }
            },
            series: [
                {
                    name: 'Produksi Padi GKG (jt ton)',
                    type: 'column',
                    data: [53.14, 60.21]
                },
                {
                    name: 'Luas Panen (jt ha)',
                    type: 'column',
                    data: [10.05, 11.32]
                },
                {
                    name: 'Produktivitas (ton/ha)',
                    type: 'line',
                    data: [5.29, 5.32]
                }
            ],
            colors: [PALETTE.emerald, PALETTE.amber, PALETTE.sky],
            xaxis: {
                categories: ['Tahun 2024', 'Tahun 2025'],
                labels: {
                    style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontWeight: '600' }
                }
            },
            yaxis: [
                {
                    seriesName: 'Produksi Padi GKG (jt ton)',
                    title: {
                        text: 'Juta Ton / Juta Hektare',
                        style: { color: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontSize: '11px' }
                    },
                    labels: {
                        style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif" },
                        formatter: (val) => (typeof val === 'number' ? `${val}` : '')
                    },
                    min: 0,
                    max: 70
                },
                {
                    seriesName: 'Luas Panen (jt ha)',
                    show: false,
                    min: 0,
                    max: 70
                },
                {
                    seriesName: 'Produktivitas (ton/ha)',
                    opposite: true,
                    title: {
                        text: 'Produktivitas (ton/ha)',
                        style: { color: PALETTE.sky, fontFamily: "'Plus Jakarta Sans', sans-serif", fontSize: '11px', fontWeight: '700' }
                    },
                    labels: {
                        style: { colors: PALETTE.sky, fontFamily: "'Plus Jakarta Sans', sans-serif", fontWeight: '700' },
                        formatter: (val) => (typeof val === 'number' ? val.toFixed(2) : '')
                    },
                    min: 4.5,
                    max: 6.0
                }
            ],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (y, { seriesIndex }) {
                        if (typeof y !== 'number') return '-';
                        if (seriesIndex === 2) return `${formatNumber(y, 2)} ton/ha`;
                        if (seriesIndex === 1) return `${formatNumber(y, 2)} juta ha`;
                        return `${formatNumber(y, 2)} juta ton`;
                    }
                }
            }
        });

        mixedChart = new ApexCharts(mixedEl, options);
        mixedChart.render();
    }

    // =========================================================================
    // 2. CHART.JS: DOUGHNUT DEKOMPOSISI PERTUMBUHAN
    // =========================================================================
    const canvasDecomp = document.getElementById('chart-growth-decomp');
    if (canvasDecomp) {
        const baseDefaults = getChartJsDefaults();

        decompChart = new Chart(canvasDecomp, {
            type: 'doughnut',
            data: {
                labels: ['Efek Luas Panen (95,27%)', 'Efek Produktivitas (4,73%)'],
                datasets: [{
                    data: [95.27, 4.73],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.9)',
                        'rgba(245, 158, 11, 0.9)'
                    ],
                    borderColor: [
                        PALETTE.emerald,
                        PALETTE.amber
                    ],
                    borderWidth: 2,
                    hoverOffset: 6,
                    shadowColor: 'rgba(16, 185, 129, 0.25)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    ...baseDefaults.plugins,
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: c.textSecondary,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' },
                            padding: 16
                        }
                    },
                    tooltip: {
                        ...baseDefaults.plugins.tooltip,
                        callbacks: {
                            label(context) {
                                return ` ${context.label}: ${context.raw}% dari total pertumbuhan`;
                            }
                        }
                    }
                }
            }
        });
    }

    // =========================================================================
    // 3. APEXCHARTS: SENSITIVITAS RENDEMEN
    // =========================================================================
    const sensitivityEl = document.getElementById('chart-yield-sensitivity');
    if (sensitivityEl) {
        const ranges = [55, 55.5, 56, 56.5, 57, 57.5, 57.62, 57.65, 58, 58.5, 59, 59.5, 60, 60.5, 61, 61.5, 62];
        const yields2024 = ranges.map(r => parseFloat((53.14 * (r / 100)).toFixed(2)));
        const yields2025 = ranges.map(r => parseFloat((60.21 * (r / 100)).toFixed(2)));

        const options = getApexThemeDefaults({
            chart: {
                type: 'area',
                height: 300,
                dropShadow: {
                    enabled: true,
                    top: 3,
                    left: 0,
                    blur: 6,
                    opacity: 0.22,
                    color: PALETTE.emerald,
                }
            },
            dataLabels: { enabled: false },
            series: [
                {
                    name: 'Produksi Beras 2025 (GKG 60,21 jt ton)',
                    data: yields2025
                },
                {
                    name: 'Produksi Beras 2024 (GKG 53,14 jt ton)',
                    data: yields2024
                }
            ],
            colors: [PALETTE.emerald, PALETTE.slate],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 95, 100]
                }
            },
            xaxis: {
                categories: ranges.map(r => `${r}%`),
                title: {
                    text: 'Rendemen Giling GKG ke Beras (%)',
                    style: { color: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontSize: '11px' }
                },
                labels: {
                    style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif" }
                }
            },
            yaxis: {
                title: {
                    text: 'Estimasi Hasil Beras (juta ton)',
                    style: { color: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontSize: '11px' }
                },
                labels: {
                    style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif" },
                    formatter: (val) => (typeof val === 'number' ? val.toFixed(1) : '')
                }
            },
            tooltip: {
                y: {
                    formatter: (val) => `${formatNumber(val, 2)} juta ton`
                }
            }
        });

        sensitivityChart = new ApexCharts(sensitivityEl, options);
        sensitivityChart.render();
    }
}

function updateProductionTheme() {
    const c = getThemeColors();

    if (mixedChart) {
        mixedChart.updateOptions({
            theme: { mode: c.isDark ? 'dark' : 'light' },
            grid: { borderColor: c.gridLine },
            xaxis: { labels: { style: { colors: c.textSecondary } } },
            yaxis: [
                { title: { style: { color: c.textSecondary } }, labels: { style: { colors: c.textSecondary } } },
                { show: false },
                { title: { style: { color: PALETTE.sky } }, labels: { style: { colors: PALETTE.sky } } }
            ]
        });
    }

    if (decompChart) {
        decompChart.options.plugins.legend.labels.color = c.textSecondary;
        decompChart.update();
    }

    if (sensitivityChart) {
        sensitivityChart.updateOptions({
            theme: { mode: c.isDark ? 'dark' : 'light' },
            grid: { borderColor: c.gridLine },
            xaxis: {
                title: { style: { color: c.textSecondary } },
                labels: { style: { colors: c.textSecondary } }
            },
            yaxis: {
                title: { style: { color: c.textSecondary } },
                labels: { style: { colors: c.textSecondary } }
            }
        });
    }
}
