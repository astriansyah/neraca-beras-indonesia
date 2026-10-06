import { Chart, registerables } from 'chart.js';
import ApexCharts from 'apexcharts';
import {
    PALETTE,
    getThemeColors,
    formatNumber,
    formatPercent,
    chartJsShadowPlugin,
    getChartJsDefaults,
    getApexThemeDefaults,
} from '../charts/theme.js';

Chart.register(...registerables, chartJsShadowPlugin);

let donutChart = null;
let paretoChart = null;
let hhiChart = null;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initTradeCharts().catch(console.error));
} else {
    initTradeCharts().catch(console.error);
}

window.addEventListener('nb-theme-change', () => {
    updateTradeTheme();
});

async function initTradeCharts() {
    const c = getThemeColors();
    const data = window.__NB_TRADE__ || null;

    // =========================================================================
    // 1. CHART.JS: DOUGHNUT NEGARA ASAL IMPOR 2024
    // =========================================================================
    const canvasDonut = document.getElementById('chart-trade-origin-donut');
    if (canvasDonut) {
        const baseDefaults = getChartJsDefaults();

        donutChart = new Chart(canvasDonut, {
            type: 'doughnut',
            data: {
                labels: ['Thailand (30,09%)', 'Vietnam (27,65%)', 'Lainnya (42,26%)'],
                datasets: [{
                    data: [1.36, 1.25, 1.91],
                    backgroundColor: [
                        'rgba(2, 132, 199, 0.9)',
                        'rgba(16, 185, 129, 0.9)',
                        'rgba(100, 116, 139, 0.85)',
                    ],
                    borderColor: [
                        PALETTE.sky,
                        PALETTE.emerald,
                        PALETTE.slate,
                    ],
                    borderWidth: 2,
                    hoverOffset: 6,
                    shadowColor: 'rgba(2, 132, 199, 0.25)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    ...baseDefaults.plugins,
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: c.textSecondary,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' },
                            padding: 14,
                        }
                    },
                    tooltip: {
                        ...baseDefaults.plugins.tooltip,
                        callbacks: {
                            label(context) {
                                return ` ${context.label}: ${context.raw} juta ton`;
                            }
                        }
                    }
                }
            }
        });
    }

    // =========================================================================
    // 2. APEXCHARTS: DIAGRAM PARETO PEMASOK BERAS 2024
    // =========================================================================
    const paretoEl = document.getElementById('chart-trade-pareto');
    if (paretoEl) {
        const options = getApexThemeDefaults({
            chart: {
                type: 'line',
                height: 320,
                stacked: false,
            },
            stroke: {
                width: [0, 3],
                curve: 'smooth',
            },
            plotOptions: {
                bar: {
                    columnWidth: '40%',
                    borderRadius: 6,
                }
            },
            series: [
                {
                    name: 'Volume Impor (Juta Ton)',
                    type: 'column',
                    data: [1.91, 1.36, 1.25]
                },
                {
                    name: 'Persentase Kumulatif (%)',
                    type: 'line',
                    data: [42.26, 72.35, 100.0]
                }
            ],
            colors: [PALETTE.sky, PALETTE.amber],
            xaxis: {
                categories: ['Lainnya (Gabungan)', 'Thailand', 'Vietnam'],
                labels: {
                    style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontWeight: '600' }
                }
            },
            yaxis: [
                {
                    seriesName: 'Volume Impor (Juta Ton)',
                    title: {
                        text: 'Juta Ton',
                        style: { color: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontSize: '11px' }
                    },
                    labels: {
                        style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif" },
                        formatter: (val) => (typeof val === 'number' ? `${val.toFixed(2)}` : '')
                    },
                    min: 0,
                    max: 2.5
                },
                {
                    seriesName: 'Persentase Kumulatif (%)',
                    opposite: true,
                    title: {
                        text: 'Kumulatif (%)',
                        style: { color: PALETTE.amber, fontFamily: "'Plus Jakarta Sans', sans-serif", fontSize: '11px', fontWeight: '700' }
                    },
                    labels: {
                        style: { colors: PALETTE.amber, fontFamily: "'Plus Jakarta Sans', sans-serif", fontWeight: '700' },
                        formatter: (val) => (typeof val === 'number' ? `${val.toFixed(0)}%` : '')
                    },
                    min: 0,
                    max: 100
                }
            ],
            annotations: {
                yaxis: [
                    {
                        y: 80,
                        yAxisIndex: 1,
                        borderColor: PALETTE.rose,
                        strokeDashArray: 4,
                        label: {
                            borderColor: PALETTE.rose,
                            style: {
                                color: '#fff',
                                background: PALETTE.rose,
                                fontSize: '11px',
                                fontFamily: "'Plus Jakarta Sans', sans-serif",
                                fontWeight: '700',
                            },
                            text: 'Garis Batas Pareto (80%)'
                        }
                    }
                ]
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (y, { seriesIndex }) {
                        if (typeof y !== 'number') return '-';
                        if (seriesIndex === 1) return `${formatNumber(y, 2)}% kumulatif`;
                        return `${formatNumber(y, 2)} juta ton`;
                    }
                }
            }
        });

        paretoChart = new ApexCharts(paretoEl, options);
        paretoChart.render();
    }

    // =========================================================================
    // 3. APEXCHARTS: RENTANG HHI (HERFINDAHL-HIRSCHMAN INDEX)
    // =========================================================================
    const hhiEl = document.getElementById('chart-trade-hhi');
    if (hhiEl) {
        const options = getApexThemeDefaults({
            chart: {
                type: 'bar',
                height: 320,
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    borderRadius: 6,
                }
            },
            series: [
                {
                    name: 'Batas Bawah HHI (Infinitesimal)',
                    data: [1669.9, 2482.4]
                },
                {
                    name: 'Batas Atas HHI (Monolitik)',
                    data: [3456.2, 5420.0]
                }
            ],
            colors: [PALETTE.emerald, PALETTE.amber],
            xaxis: {
                categories: ['Tahun 2024', 'Tahun 2025 (Estimasi)'],
                labels: {
                    style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontWeight: '600' }
                }
            },
            yaxis: {
                title: {
                    text: 'Poin Indeks HHI',
                    style: { color: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontSize: '11px' }
                },
                labels: {
                    style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif" },
                    formatter: (val) => (typeof val === 'number' ? formatNumber(val, 0) : '')
                },
                min: 0,
                max: 6000
            },
            annotations: {
                yaxis: [
                    {
                        y: 1500,
                        borderColor: PALETTE.emerald,
                        strokeDashArray: 3,
                        label: {
                            borderColor: PALETTE.emerald,
                            style: { color: '#fff', background: PALETTE.emerald, fontSize: '10px', fontWeight: '700' },
                            text: 'Ambang Batas Kompetitif (1.500)'
                        }
                    },
                    {
                        y: 2500,
                        borderColor: PALETTE.rose,
                        strokeDashArray: 3,
                        label: {
                            borderColor: PALETTE.rose,
                            style: { color: '#fff', background: PALETTE.rose, fontSize: '10px', fontWeight: '700' },
                            text: 'Ambang Sangat Terkonsentrasi (2.500)'
                        }
                    }
                ]
            },
            tooltip: {
                y: {
                    formatter: (val) => `${formatNumber(val, 1)} poin`
                }
            }
        });

        hhiChart = new ApexCharts(hhiEl, options);
        hhiChart.render();
    }
}

function updateTradeTheme() {
    const c = getThemeColors();

    if (donutChart) {
        donutChart.options.plugins.legend.labels.color = c.textSecondary;
        donutChart.update();
    }

    if (paretoChart) {
        paretoChart.updateOptions({
            theme: { mode: c.isDark ? 'dark' : 'light' },
            grid: { borderColor: c.gridLine },
            xaxis: { labels: { style: { colors: c.textSecondary } } },
            yaxis: [
                { title: { style: { color: c.textSecondary } }, labels: { style: { colors: c.textSecondary } } },
                { title: { style: { color: PALETTE.amber } }, labels: { style: { colors: PALETTE.amber } } }
            ]
        });
    }

    if (hhiChart) {
        hhiChart.updateOptions({
            theme: { mode: c.isDark ? 'dark' : 'light' },
            grid: { borderColor: c.gridLine },
            xaxis: { labels: { style: { colors: c.textSecondary } } },
            yaxis: { title: { style: { color: c.textSecondary } }, labels: { style: { colors: c.textSecondary } } }
        });
    }
}
