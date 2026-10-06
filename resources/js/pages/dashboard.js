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

// Register Chart.js components + custom soft shadow plugin
Chart.register(...registerables, chartJsShadowPlugin);

let groupedChart = null;
let ssrRadialChart = null;
let yoyBarChart = null;
let sparklines = [];

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initDashboardCharts().catch(console.error));
} else {
    initDashboardCharts().catch(console.error);
}

// Dengarkan perubahan tema dark / light
window.addEventListener('nb-theme-change', () => {
    updateChartsTheme();
});

// Dengarkan perubahan filter tahun
window.addEventListener('nb-year-filter', (e) => {
    const year = e.detail?.year || 'compare';
    handleYearFilter(year);
});

async function initDashboardCharts() {
    let data = window.__NB_STATS__;
    if (!data) {
        try {
            const res = await fetch('/api/stats/summary');
            const json = await res.json();
            data = json.data;
        } catch (e) {
            console.warn('Menggunakan fallback data statis untuk chart dashboard.');
            data = null;
        }
    }

    const c = getThemeColors();

    // =========================================================================
    // 1. SPARKLINE PADA KPI CARDS (ApexCharts mini)
    // =========================================================================
    const sparkConfig = (elementId, values, color) => {
        const el = document.getElementById(elementId);
        if (!el) return null;
        const opt = {
            series: [{ data: values }],
            chart: {
                type: 'line',
                height: 40,
                sparkline: { enabled: true },
                animations: { enabled: true, speed: 600 },
                dropShadow: { enabled: true, top: 2, left: 0, blur: 4, opacity: 0.25, color: color }
            },
            stroke: { curve: 'smooth', width: 2.5, colors: [color] },
            tooltip: {
                fixed: { enabled: false },
                x: { show: false },
                y: {
                    title: { formatter: () => '' },
                    formatter: (val) => `${formatNumber(val, 2)}`
                },
                marker: { show: false }
            },
            colors: [color]
        };
        const chart = new ApexCharts(el, opt);
        chart.render();
        return chart;
    };

    sparklines.push(sparkConfig('spark-produksi', [30.62, 34.71], PALETTE.emerald));
    sparklines.push(sparkConfig('spark-konsumsi', [26.06, 31.10], PALETTE.amber));
    sparklines.push(sparkConfig('spark-impor', [4.52, 0.45], PALETTE.sky));
    sparklines.push(sparkConfig('spark-stok', [2.00, 3.25], PALETTE.purple));
    sparklines.push(sparkConfig('spark-ssr', [87.14, 98.72], PALETTE.emerald));

    // =========================================================================
    // 2. CHART.JS: GROUPED BAR CHART PERBANDINGAN NERACA
    // =========================================================================
    const canvasSummary = document.getElementById('chart-grouped-summary');
    if (canvasSummary) {
        const baseDefaults = getChartJsDefaults();

        groupedChart = new Chart(canvasSummary, {
            type: 'bar',
            data: {
                labels: ['Produksi Beras', 'Konsumsi Nasional', 'Volume Impor', 'Stok CBP Bulog'],
                datasets: [
                    {
                        label: 'Tahun 2024',
                        data: [30.62, 26.06, 4.52, 2.00],
                        backgroundColor: 'rgba(100, 116, 139, 0.85)',
                        borderColor: PALETTE.slate,
                        borderWidth: 1.5,
                        borderRadius: 8,
                        shadowColor: 'rgba(100, 116, 139, 0.25)',
                    },
                    {
                        label: 'Tahun 2025',
                        data: [34.71, 31.10, 0.45, 3.25],
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderColor: PALETTE.emerald,
                        borderWidth: 1.5,
                        borderRadius: 8,
                        shadowColor: 'rgba(16, 185, 129, 0.35)',
                    }
                ]
            },
            options: {
                ...baseDefaults,
                plugins: {
                    ...baseDefaults.plugins,
                    tooltip: {
                        ...baseDefaults.plugins.tooltip,
                        callbacks: {
                            label(context) {
                                return ` ${context.dataset.label}: ${formatNumber(context.raw, 2)} juta ton`;
                            }
                        }
                    }
                },
                scales: {
                    ...baseDefaults.scales,
                    y: {
                        ...baseDefaults.scales.y,
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

    // =========================================================================
    // 3. APEXCHARTS: RADIAL BAR SSR & IDR
    // =========================================================================
    const radialEl = document.getElementById('chart-ssr-idr');
    if (radialEl) {
        const ssrVal = data?.kpis?.ssr?.val_2025 ?? 98.72;
        const idrVal = data?.kpis?.idr?.val_2025 ?? 1.28;

        const radialOptions = getApexThemeDefaults({
            chart: {
                type: 'radialBar',
                height: 330,
            },
            series: [Math.round(ssrVal * 10) / 10, Math.round(idrVal * 10) / 10],
            plotOptions: {
                radialBar: {
                    offsetY: -5,
                    startAngle: 0,
                    endAngle: 360,
                    hollow: {
                        margin: 5,
                        size: '42%',
                        background: 'transparent',
                    },
                    track: {
                        background: c.isDark ? 'rgba(51, 65, 85, 0.4)' : '#f1f5f9',
                        strokeWidth: '95%',
                    },
                    dataLabels: {
                        name: {
                            fontSize: '13px',
                            fontWeight: '700',
                            fontFamily: "'Plus Jakarta Sans', sans-serif",
                            color: c.textSecondary,
                            offsetY: -5,
                        },
                        value: {
                            fontSize: '18px',
                            fontWeight: '800',
                            fontFamily: "'Plus Jakarta Sans', sans-serif",
                            color: c.textPrimary,
                            formatter: (val) => `${val}%`,
                            offsetY: 5,
                        },
                        total: {
                            show: true,
                            label: 'SSR 2025',
                            formatter: () => `${formatPercent(ssrVal, 1)}`,
                            color: PALETTE.emerald,
                            fontSize: '14px',
                            fontWeight: '800',
                            fontFamily: "'Plus Jakarta Sans', sans-serif",
                        }
                    }
                }
            },
            colors: [PALETTE.emerald, PALETTE.sky],
            labels: ['Swasembada (SSR)', 'Ketergantungan Impor (IDR)'],
            legend: {
                show: true,
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '12px',
                fontFamily: "'Plus Jakarta Sans', sans-serif",
                itemMargin: { horizontal: 10, vertical: 5 },
                labels: { colors: c.textSecondary }
            }
        });

        ssrRadialChart = new ApexCharts(radialEl, radialOptions);
        ssrRadialChart.render();
    }

    // =========================================================================
    // 4. APEXCHARTS: HORIZONTAL BAR YOY COMPARISON
    // =========================================================================
    const yoyEl = document.getElementById('chart-yoy-comparison');
    if (yoyEl) {
        const categories = [
            'Produksi GKG',
            'Luas Panen',
            'Produktivitas Lahan',
            'Konsumsi Beras',
            'Volume Impor',
            'Stok CBP Bulog'
        ];
        const yoyValues = [13.30, 12.64, 0.59, 19.34, -90.03, 62.42];

        const barOptions = getApexThemeDefaults({
            chart: {
                type: 'bar',
                height: 320,
            },
            series: [{
                name: 'Pertumbuhan YoY (%)',
                data: yoyValues
            }],
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 6,
                    barHeight: '62%',
                    colors: {
                        ranges: [
                            { from: -100, to: 0, color: PALETTE.emerald }, // Penurunan impor adalah kabar baik (hijau)
                            { from: 0.01, to: 15, color: PALETTE.emerald },
                            { from: 15.01, to: 30, color: PALETTE.amber },
                            { from: 30.01, to: 100, color: PALETTE.purple }
                        ]
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: (val) => `${val > 0 ? '+' : ''}${formatNumber(val, 2)}%`,
                style: {
                    fontSize: '11px',
                    fontFamily: "'Plus Jakarta Sans', sans-serif",
                    fontWeight: '700',
                    colors: ['#fff']
                },
                offsetX: 2
            },
            xaxis: {
                categories: categories,
                labels: {
                    formatter: (val) => `${val}%`,
                    style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif" }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: c.textPrimary, fontWeight: '600', fontFamily: "'Plus Jakarta Sans', sans-serif" }
                }
            },
            tooltip: {
                y: {
                    formatter: (val) => `${val > 0 ? '+' : ''}${formatNumber(val, 2)}% YoY`
                }
            }
        });

        yoyBarChart = new ApexCharts(yoyEl, barOptions);
        yoyBarChart.render();
    }
}

function handleYearFilter(year) {
    if (!groupedChart) return;

    if (year === '2024') {
        groupedChart.show(0);
        groupedChart.hide(1);
    } else if (year === '2025') {
        groupedChart.hide(0);
        groupedChart.show(1);
    } else {
        groupedChart.show(0);
        groupedChart.show(1);
    }
    groupedChart.update();
}

function updateChartsTheme() {
    const c = getThemeColors();

    if (groupedChart) {
        const defaults = getChartJsDefaults();
        groupedChart.options.scales.x.ticks.color = c.textSecondary;
        groupedChart.options.scales.y.ticks.color = c.textSecondary;
        groupedChart.options.scales.y.grid.color = c.gridLine;
        groupedChart.options.plugins.legend.labels.color = c.textSecondary;
        groupedChart.update();
    }

    if (ssrRadialChart) {
        ssrRadialChart.updateOptions({
            theme: { mode: c.isDark ? 'dark' : 'light' },
            grid: { borderColor: c.gridLine },
            plotOptions: {
                radialBar: {
                    track: { background: c.isDark ? 'rgba(51, 65, 85, 0.4)' : '#f1f5f9' },
                    dataLabels: {
                        name: { color: c.textSecondary },
                        value: { color: c.textPrimary }
                    }
                }
            }
        });
    }

    if (yoyBarChart) {
        yoyBarChart.updateOptions({
            theme: { mode: c.isDark ? 'dark' : 'light' },
            grid: { borderColor: c.gridLine },
            xaxis: { labels: { style: { colors: c.textSecondary } } },
            yaxis: { labels: { style: { colors: c.textPrimary } } }
        });
    }
}
