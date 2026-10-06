import { Chart, registerables } from 'chart.js';
import ApexCharts from 'apexcharts';
import * as d3 from 'd3';
import {
    PALETTE,
    getThemeColors,
    formatNumber,
    chartJsShadowPlugin,
    getChartJsDefaults,
    getApexThemeDefaults,
    appendD3DropShadowFilter,
} from '../charts/theme.js';

Chart.register(...registerables, chartJsShadowPlugin);

let timelineChart = null;
let decompChart = null;
let currentScenario = '31_10'; // default scenario

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initStockCharts().catch(console.error));
} else {
    initStockCharts().catch(console.error);
}

window.addEventListener('nb-theme-change', () => {
    updateStockTheme();
});

window.addEventListener('nb-stock-scenario', (e) => {
    currentScenario = e.detail?.scenario || '31_10';
    renderD3BulletChart();
});

async function initStockCharts() {
    const c = getThemeColors();

    // =========================================================================
    // 1. CHART.JS: TIMELINE AKUMULASI STOK BULOG (LINE AREA)
    // =========================================================================
    const canvasTimeline = document.getElementById('chart-stock-timeline');
    if (canvasTimeline) {
        const baseDefaults = getChartJsDefaults();

        timelineChart = new Chart(canvasTimeline, {
            type: 'line',
            data: {
                labels: ['Awal Tahun 2024', 'Akhir Tahun 2024', 'Akhir Tahun 2025 (Rekor)'],
                datasets: [{
                    label: 'Stok CBP Bulog (juta ton)',
                    data: [1.33, 2.00, 3.25],
                    borderColor: PALETTE.purple,
                    backgroundColor: 'rgba(139, 92, 246, 0.15)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: PALETTE.purple,
                    pointBorderWidth: 2.5,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    shadowColor: 'rgba(139, 92, 246, 0.35)',
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
                                return ` Stok CBP: ${formatNumber(context.raw, 2)} juta ton`;
                            },
                            afterLabel(context) {
                                if (context.dataIndex === 0) return ' Rentang estimasi: 1,26–1,40 juta ton';
                                if (context.dataIndex === 1) return ' Realisasi resmi penutupan 2024';
                                return ' Rekor serapan beras lokal 3,20 juta ton';
                            }
                        }
                    }
                },
                scales: {
                    ...baseDefaults.scales,
                    y: {
                        ...baseDefaults.scales.y,
                        min: 0.5,
                        max: 4.0,
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
    // 2. D3.JS: BULLET CHART BULAN CAKUPAN STOK BULOG
    // =========================================================================
    renderD3BulletChart();

    // =========================================================================
    // 3. APEXCHARTS: DEKOMPOSISI PERUBAHAN STOK VS RESIDUAL
    // =========================================================================
    const decompEl = document.getElementById('chart-stock-decomposition');
    if (decompEl) {
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
                    name: 'Penambahan Stok CBP Bulog (ΔStok)',
                    data: [0.67, 1.25]
                },
                {
                    name: 'Residual Non-Bulog (Pedagang/RT/Susut)',
                    data: [8.41, 2.36]
                }
            ],
            colors: [PALETTE.purple, PALETTE.amber],
            xaxis: {
                categories: ['Tahun 2024 (Total +9,08 jt ton)', 'Tahun 2025 (Total +3,61 jt ton)'],
                labels: {
                    style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontWeight: '600' }
                }
            },
            yaxis: {
                title: {
                    text: 'Juta Ton Surplus',
                    style: { color: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontSize: '11px' }
                },
                labels: {
                    style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif" },
                    formatter: (val) => (typeof val === 'number' ? `${val.toFixed(2)}` : '')
                },
                min: 0,
                max: 10
            },
            tooltip: {
                y: {
                    formatter: (val) => `${formatNumber(val, 2)} juta ton`
                }
            }
        });

        decompChart = new ApexCharts(decompEl, options);
        decompChart.render();
    }
}

/**
 * Render visualisasi Bullet Chart D3.js untuk Bulan Cakupan Stok Bulog
 */
function renderD3BulletChart() {
    const container = document.getElementById('chart-stock-bullet');
    if (!container) return;

    // Bersihkan SVG lama jika ada
    container.innerHTML = '';

    const c = getThemeColors();
    const width = container.clientWidth || 360;
    const height = container.clientHeight || 280;
    const margin = { top: 35, right: 30, bottom: 45, left: 30 };
    const innerWidth = width - margin.left - margin.right;
    const innerHeight = height - margin.top - margin.bottom;

    const val2025 = currentScenario === '31_10' ? 1.25 : 1.50;
    const val2024 = currentScenario === '31_10' ? 0.77 : 0.92;
    const target = 3.00; // Standar aman FAO (3 bulan kebutuhan)

    const svg = d3.select(container)
        .append('svg')
        .attr('width', width)
        .attr('height', height)
        .attr('viewBox', `0 0 ${width} ${height}`)
        .attr('class', 'overflow-visible');

    const filterUrl = appendD3DropShadowFilter(svg, 'stock-bullet-shadow');

    const g = svg.append('g')
        .attr('transform', `translate(${margin.left}, ${margin.top})`);

    const xScale = d3.scaleLinear()
        .domain([0, 3.5])
        .range([0, innerWidth]);

    // Zona Kategori Latar Belakang (Qualitative Ranges)
    // 0 - 1.0 bln: Kritis (< 30 hari)
    // 1.0 - 2.5 bln: Moderat (30-75 hari)
    // 2.5 - 3.5 bln: Aman (>= 75 hari)
    const ranges = [
        { from: 0, to: 1.0, color: c.isDark ? 'rgba(239, 68, 68, 0.25)' : 'rgba(254, 226, 226, 0.8)', label: 'Kritis (<1 bln)' },
        { from: 1.0, to: 2.5, color: c.isDark ? 'rgba(245, 158, 11, 0.20)' : 'rgba(254, 243, 199, 0.7)', label: 'Waspada (1–2,5 bln)' },
        { from: 2.5, to: 3.5, color: c.isDark ? 'rgba(16, 185, 129, 0.20)' : 'rgba(209, 250, 229, 0.7)', label: 'Aman (≥2,5 bln)' }
    ];

    const barHeight = Math.min(innerHeight * 0.42, 48);
    const barY = (innerHeight - barHeight) / 2;

    // Gambar rentang latar belakang
    g.selectAll('.bullet-range')
        .data(ranges)
        .enter()
        .append('rect')
        .attr('class', 'bullet-range')
        .attr('x', d => xScale(d.from))
        .attr('y', barY)
        .attr('width', d => Math.max(xScale(d.to) - xScale(d.from), 0))
        .attr('height', barHeight)
        .attr('rx', 6)
        .attr('fill', d => d.color);

    // Nilai Tahun 2024 (Background Bar Slate)
    g.append('rect')
        .attr('x', 0)
        .attr('y', barY + (barHeight * 0.2))
        .attr('width', xScale(val2024))
        .attr('height', barHeight * 0.6)
        .attr('rx', 4)
        .attr('fill', PALETTE.slate)
        .attr('opacity', 0.65);

    // Nilai Tahun 2025 (Primary Bar Purple) dengan filter shadow
    g.append('rect')
        .attr('x', 0)
        .attr('y', barY + (barHeight * 0.3))
        .attr('width', xScale(val2025))
        .attr('height', barHeight * 0.4)
        .attr('rx', 3)
        .attr('fill', PALETTE.purple)
        .style('filter', filterUrl);

    // Garis Target Standar FAO (3 Bulan)
    g.append('line')
        .attr('x1', xScale(target))
        .attr('x2', xScale(target))
        .attr('y1', barY - 10)
        .attr('y2', barY + barHeight + 10)
        .attr('stroke', PALETTE.rose)
        .attr('stroke-width', 3)
        .attr('stroke-dasharray', '4,3');

    // Label Target FAO
    g.append('text')
        .attr('x', xScale(target))
        .attr('y', barY - 14)
        .attr('text-anchor', 'middle')
        .attr('font-family', "'Plus Jakarta Sans', sans-serif")
        .attr('font-size', '10px')
        .attr('font-weight', '700')
        .attr('fill', PALETTE.rose)
        .text('Target FAO (3,0 bln)');

    // Text Label Nilai 2025 di atas Bar
    g.append('text')
        .attr('x', xScale(val2025) + 6)
        .attr('y', barY + (barHeight * 0.55))
        .attr('text-anchor', 'start')
        .attr('font-family', "'Plus Jakarta Sans', sans-serif")
        .attr('font-size', '12px')
        .attr('font-weight', '800')
        .attr('fill', c.isDark ? '#e9d5ff' : '#6b21a8')
        .text(`${formatNumber(val2025, 2)} bln`);

    // Sumbu X Bawah
    const xAxis = d3.axisBottom(xScale)
        .ticks(5)
        .tickFormat(d => `${d} bln`);

    const axisG = g.append('g')
        .attr('transform', `translate(0, ${barY + barHeight + 12})`)
        .call(xAxis);

    axisG.select('.domain').attr('stroke', c.gridLine);
    axisG.selectAll('.tick line').attr('stroke', c.gridLine);
    axisG.selectAll('.tick text')
        .attr('fill', c.textSecondary)
        .attr('font-family', "'Plus Jakarta Sans', sans-serif")
        .attr('font-size', '10px');

    // Legend Mini di Bawah
    const legendG = g.append('g')
        .attr('transform', `translate(0, ${innerHeight + 24})`);

    legendG.append('circle').attr('cx', 4).attr('cy', 0).attr('r', 4).attr('fill', PALETTE.purple);
    legendG.append('text').attr('x', 14).attr('y', 3).attr('font-size', '10px').attr('fill', c.textSecondary).text(`2025 (${val2025} bln)`);

    legendG.append('circle').attr('cx', 110).attr('cy', 0).attr('r', 4).attr('fill', PALETTE.slate);
    legendG.append('text').attr('x', 120).attr('y', 3).attr('font-size', '10px').attr('fill', c.textSecondary).text(`2024 (${val2024} bln)`);
}

function updateStockTheme() {
    const c = getThemeColors();

    if (timelineChart) {
        timelineChart.options.scales.x.ticks.color = c.textSecondary;
        timelineChart.options.scales.y.ticks.color = c.textSecondary;
        timelineChart.options.scales.y.grid.color = c.gridLine;
        timelineChart.update();
    }

    if (decompChart) {
        decompChart.updateOptions({
            theme: { mode: c.isDark ? 'dark' : 'light' },
            grid: { borderColor: c.gridLine },
            xaxis: { labels: { style: { colors: c.textSecondary } } },
            yaxis: { title: { style: { color: c.textSecondary } }, labels: { style: { colors: c.textSecondary } } }
        });
    }

    renderD3BulletChart();
}
