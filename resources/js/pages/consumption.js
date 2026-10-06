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

let rangeChart = null;
let consistencyChart = null;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initConsumptionCharts().catch(console.error));
} else {
    initConsumptionCharts().catch(console.error);
}

window.addEventListener('nb-theme-change', () => {
    updateConsumptionTheme();
});

async function initConsumptionCharts() {
    const c = getThemeColors();

    // =========================================================================
    // 1. APEXCHARTS: RANGEBAR KETIDAKPASTIAN KONSUMSI 2025
    // =========================================================================
    const rangeEl = document.getElementById('chart-consumption-range');
    if (rangeEl) {
        const options = getApexThemeDefaults({
            chart: {
                type: 'rangeBar',
                height: 290,
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '45%',
                    borderRadius: 6,
                }
            },
            series: [
                {
                    name: 'Rentang Nilai',
                    data: [
                        {
                            x: 'Realisasi 2024',
                            y: [26.06, 26.06],
                            fillColor: PALETTE.slate
                        },
                        {
                            x: 'Rentang Konsumsi 2025',
                            y: [30.00, 31.00],
                            fillColor: PALETTE.amber
                        }
                    ]
                }
            ],
            annotations: {
                xaxis: [
                    {
                        x: 31.10,
                        borderColor: PALETTE.rose,
                        strokeDashArray: 3,
                        label: {
                            borderColor: PALETTE.rose,
                            style: {
                                color: '#fff',
                                background: PALETTE.rose,
                                fontSize: '11px',
                                fontFamily: "'Plus Jakarta Sans', sans-serif",
                                fontWeight: '700'
                            },
                            text: 'Estimasi Resmi: 31,10 jt ton'
                        }
                    }
                ]
            },
            xaxis: {
                min: 24,
                max: 33,
                title: {
                    text: 'Volume Konsumsi (Juta Ton)',
                    style: { color: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontSize: '11px' }
                },
                labels: {
                    formatter: (val) => `${val}`,
                    style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif" }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: c.textPrimary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontWeight: '600' }
                }
            },
            tooltip: {
                custom: function ({ seriesIndex, dataPointIndex, w }) {
                    const data = w.globals.initialSeries[seriesIndex].data[dataPointIndex];
                    if (data.x === 'Realisasi 2024') {
                        return `<div class="p-2 text-xs"><strong>2024:</strong> 26,06 juta ton (Titik Realisasi)</div>`;
                    }
                    return `<div class="p-2 text-xs"><strong>Rentang 2025:</strong> 30,00 – 31,00 juta ton<br><span class="text-rose-500 font-bold">Estimasi Resmi: 31,10 juta ton</span></div>`;
                }
            }
        });

        rangeChart = new ApexCharts(rangeEl, options);
        rangeChart.render();
    }

    // =========================================================================
    // 2. CHART.JS: UJI KONSISTENSI DEMOGRAFIS 2025
    // =========================================================================
    const canvasConsist = document.getElementById('chart-population-consistency');
    if (canvasConsist) {
        const baseDefaults = getChartJsDefaults();

        consistencyChart = new Chart(canvasConsist, {
            type: 'bar',
            data: {
                labels: [
                    'Total Resmi 2025',
                    'Implisit RT Maks (92,4 kg)',
                    'Implisit RT Min (91,6 kg)',
                    'Total 2024 (Realisasi)'
                ],
                datasets: [{
                    label: 'Volume Beras (juta ton)',
                    data: [31.10, 26.23, 26.01, 26.06],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                        'rgba(245, 158, 11, 0.65)',
                        'rgba(100, 116, 139, 0.75)'
                    ],
                    borderColor: [
                        PALETTE.emerald,
                        PALETTE.amber,
                        PALETTE.amber,
                        PALETTE.slate
                    ],
                    borderWidth: 1.5,
                    borderRadius: 8,
                    shadowColor: 'rgba(16, 185, 129, 0.25)',
                }]
            },
            options: {
                ...baseDefaults,
                plugins: {
                    ...baseDefaults.plugins,
                    legend: { display: false },
                    tooltip: {
                        ...baseDefaults.plugins.tooltip,
                        callbacks: {
                            label(context) {
                                return ` ${context.raw} juta ton`;
                            },
                            afterLabel(context) {
                                if (context.dataIndex === 0) return ' Mencakup konsumsi agregat (RT + Industri/Horeka)';
                                if (context.dataIndex === 1 || context.dataIndex === 2) return ' Hasil perkalian per kapita × ~284 jt jiwa';
                                return ' Realisasi konsumsi 2024';
                            }
                        }
                    }
                },
                scales: {
                    ...baseDefaults.scales,
                    y: {
                        ...baseDefaults.scales.y,
                        min: 20,
                        max: 35,
                        title: {
                            display: true,
                            text: 'Juta Ton',
                            color: c.textSecondary,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' }
                        }
                    }
                }
            }
        });
    }
}

function updateConsumptionTheme() {
    const c = getThemeColors();

    if (rangeChart) {
        rangeChart.updateOptions({
            theme: { mode: c.isDark ? 'dark' : 'light' },
            grid: { borderColor: c.gridLine },
            xaxis: { labels: { style: { colors: c.textSecondary } } },
            yaxis: { labels: { style: { colors: c.textPrimary } } }
        });
    }

    if (consistencyChart) {
        consistencyChart.options.scales.x.ticks.color = c.textSecondary;
        consistencyChart.options.scales.y.ticks.color = c.textSecondary;
        consistencyChart.options.scales.y.grid.color = c.gridLine;
        consistencyChart.update();
    }
}
