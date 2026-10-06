import { Chart, registerables } from 'chart.js';
import ApexCharts from 'apexcharts';
import * as d3 from 'd3';
import { sankey as d3Sankey, sankeyLinkHorizontal } from 'd3-sankey';
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

let waterfallChart = null;
let radarChart = null;
let currentSankeyYear = '2025';

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initBalanceCharts().catch(console.error));
} else {
    initBalanceCharts().catch(console.error);
}

window.addEventListener('nb-theme-change', () => {
    updateBalanceTheme();
});

window.addEventListener('nb-sankey-year', (e) => {
    currentSankeyYear = e.detail?.year || '2025';
    renderD3Sankey();
});

async function initBalanceCharts() {
    const c = getThemeColors();

    // =========================================================================
    // 1. D3.JS: INTERACTIVE SANKEY DIAGRAM (Rantai Pasok & Aliran Beras)
    // =========================================================================
    renderD3Sankey();

    // =========================================================================
    // 2. APEXCHARTS: WATERFALL CHART NERACA KOTOR 2025
    // =========================================================================
    const waterfallEl = document.getElementById('chart-balance-waterfall');
    if (waterfallEl) {
        const options = getApexThemeDefaults({
            chart: {
                type: 'bar',
                height: 340,
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    borderRadius: 6,
                    colors: {
                        ranges: [
                            { from: -100, to: -0.0001, color: PALETTE.amber }, // Pengurangan (Konsumsi & Ekspor)
                            { from: 0.0001, to: 30, color: PALETTE.sky },     // Impor / Surplus
                            { from: 30.01, to: 40, color: PALETTE.emerald }    // Produksi Domestik
                        ]
                    }
                }
            },
            series: [{
                name: 'Volume Aliran',
                data: [
                    { x: 'Produksi Domestik', y: 34.71, fillColor: PALETTE.emerald },
                    { x: 'Impor Beras', y: 0.45, fillColor: PALETTE.sky },
                    { x: 'Konsumsi Nasional', y: -31.10, fillColor: PALETTE.amber },
                    { x: 'Ekspor Beras', y: -0.01, fillColor: PALETTE.rose },
                    { x: 'Surplus Neraca Kotor', y: 3.61, fillColor: PALETTE.emerald }
                ]
            }],
            dataLabels: {
                enabled: true,
                formatter: (val) => `${val > 0 ? '+' : ''}${formatNumber(val, 2)}`,
                style: {
                    fontSize: '11px',
                    fontFamily: "'Plus Jakarta Sans', sans-serif",
                    fontWeight: '700',
                    colors: ['#fff']
                }
            },
            xaxis: {
                labels: {
                    rotate: -15,
                    style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontWeight: '600', fontSize: '11px' }
                }
            },
            yaxis: {
                title: {
                    text: 'Juta Ton',
                    style: { color: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif", fontSize: '11px' }
                },
                labels: {
                    style: { colors: c.textSecondary, fontFamily: "'Plus Jakarta Sans', sans-serif" },
                    formatter: (val) => (typeof val === 'number' ? `${val}` : '')
                }
            },
            tooltip: {
                y: {
                    formatter: (val) => `${val > 0 ? '+' : ''}${formatNumber(val, 2)} juta ton`
                }
            }
        });

        waterfallChart = new ApexCharts(waterfallEl, options);
        waterfallChart.render();
    }

    // =========================================================================
    // 3. CHART.JS: RADAR CHART INDEKS KEDAULATAN & KETAHANAN PANGAN
    // =========================================================================
    const canvasRadar = document.getElementById('chart-balance-radar');
    if (canvasRadar) {
        const baseDefaults = getChartJsDefaults();

        radarChart = new Chart(canvasRadar, {
            type: 'radar',
            data: {
                labels: [
                    'Swasembada (SSR)',
                    'Kemerdekaan Impor',
                    'Kecukupan Domestik',
                    'Keamanan Cadangan',
                    'Kemandirian Pasokan'
                ],
                datasets: [
                    {
                        label: 'Tahun 2024',
                        data: [87.14, 87.14, 117.50, 30.7, 85.0],
                        borderColor: PALETTE.slate,
                        backgroundColor: 'rgba(100, 116, 139, 0.20)',
                        borderWidth: 2,
                        pointBackgroundColor: PALETTE.slate,
                        pointBorderColor: '#fff',
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Tahun 2025',
                        data: [98.72, 98.72, 111.61, 41.7, 98.0],
                        borderColor: PALETTE.emerald,
                        backgroundColor: 'rgba(16, 185, 129, 0.25)',
                        borderWidth: 2.5,
                        pointBackgroundColor: PALETTE.emerald,
                        pointBorderColor: '#fff',
                        pointHoverRadius: 7,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    ...baseDefaults.plugins,
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: c.textSecondary,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: '600' },
                            padding: 16
                        }
                    },
                    tooltip: {
                        ...baseDefaults.plugins.tooltip,
                        callbacks: {
                            label(context) {
                                return ` ${context.dataset.label}: ${formatNumber(context.raw, 1)}%`;
                            }
                        }
                    }
                },
                scales: {
                    r: {
                        angleLines: { color: c.gridLine },
                        grid: { color: c.gridLine },
                        pointLabels: {
                            color: c.textPrimary,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' }
                        },
                        ticks: {
                            color: c.textSecondary,
                            backdropColor: 'transparent',
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 10 }
                        },
                        suggestedMin: 0,
                        suggestedMax: 120
                    }
                }
            }
        });
    }
}

/**
 * Render visualisasi Sankey Diagram D3.js dengan dukungan switch tahun (2024 vs 2025)
 */
function renderD3Sankey() {
    const container = document.getElementById('chart-balance-sankey');
    if (!container) return;

    container.innerHTML = '';

    const c = getThemeColors();
    const width = container.clientWidth || 900;
    const height = container.clientHeight || 440;
    const margin = { top: 20, right: 140, bottom: 20, left: 140 };

    const rawData = window.__NB_BALANCE__?.sankey?.[currentSankeyYear];
    if (!rawData || !rawData.nodes || !rawData.links) return;

    // Duplikasi data secara mendalam untuk mencegah mutasi oleh d3-sankey
    const data = {
        nodes: rawData.nodes.map(d => ({ ...d })),
        links: rawData.links.map(d => ({ ...d }))
    };

    const svg = d3.select(container)
        .append('svg')
        .attr('width', width)
        .attr('height', height)
        .attr('viewBox', `0 0 ${width} ${height}`)
        .attr('class', 'overflow-visible');

    const filterUrl = appendD3DropShadowFilter(svg, 'sankey-drop-shadow');

    // Inisialisasi generator Sankey D3
    const sankeyGen = d3Sankey()
        .nodeWidth(20)
        .nodePadding(22)
        .extent([[margin.left, margin.top], [width - margin.right, height - margin.bottom]]);

    const { nodes, links } = sankeyGen(data);

    // Palet warna kategori node
    const nodeColor = (d) => {
        if (d.category === 'inflow') {
            return d.name.includes('Produksi') ? PALETTE.emerald : PALETTE.sky;
        }
        if (d.category === 'intermediate') return '#059669'; // Emerald dark
        if (d.category === 'outflow') {
            return d.name.includes('Konsumsi') ? PALETTE.amber : PALETTE.rose;
        }
        if (d.category === 'stock') return PALETTE.purple;
        return PALETTE.slate; // residual
    };

    // Definisikan gradien SVG untuk setiap link aliran
    const defs = svg.select('defs').empty() ? svg.append('defs') : svg.select('defs');

    links.forEach((link, i) => {
        const gradId = `link-gradient-${i}-${currentSankeyYear}`;
        const grad = defs.append('linearGradient')
            .attr('id', gradId)
            .attr('gradientUnits', 'userSpaceOnUse')
            .attr('x1', link.source.x1)
            .attr('x2', link.target.x0);

        grad.append('stop')
            .attr('offset', '0%')
            .attr('stop-color', nodeColor(link.source))
            .attr('stop-opacity', 0.55);

        grad.append('stop')
            .attr('offset', '100%')
            .attr('stop-color', nodeColor(link.target))
            .attr('stop-opacity', 0.55);

        link.gradId = gradId;
    });

    // Gambar Links (Aliran Pita)
    const linkG = svg.append('g')
        .attr('fill', 'none')
        .selectAll('.sankey-link')
        .data(links)
        .enter()
        .append('path')
        .attr('class', 'sankey-link transition-opacity duration-200 cursor-pointer')
        .attr('d', sankeyLinkHorizontal())
        .attr('stroke', d => `url(#${d.gradId})`)
        .attr('stroke-width', d => Math.max(1, d.width))
        .attr('opacity', 0.8)
        .on('mouseenter', function (event, d) {
            d3.select(this).attr('opacity', 1.0).attr('stroke-width', Math.max(1, d.width) + 2);
        })
        .on('mouseleave', function (event, d) {
            d3.select(this).attr('opacity', 0.8).attr('stroke-width', Math.max(1, d.width));
        });

    linkG.append('title')
        .text(d => `${d.source.name} → ${d.target.name}\nVolume: ${formatNumber(d.value, 2)} juta ton`);

    // Gambar Nodes (Balok Batang) dengan soft shadow
    const nodeG = svg.append('g')
        .selectAll('.sankey-node')
        .data(nodes)
        .enter()
        .append('g')
        .attr('class', 'sankey-node cursor-pointer');

    nodeG.append('rect')
        .attr('x', d => d.x0)
        .attr('y', d => d.y0)
        .attr('height', d => Math.max(d.y1 - d.y0, 2))
        .attr('width', d => d.x1 - d.x0)
        .attr('rx', 4)
        .attr('fill', d => nodeColor(d))
        .style('filter', filterUrl);

    // Label Teks di samping Node
    nodeG.append('text')
        .attr('x', d => d.x0 < width / 2 ? d.x0 - 8 : d.x1 + 8)
        .attr('y', d => (d.y1 + d.y0) / 2)
        .attr('dy', '0.35em')
        .attr('text-anchor', d => d.x0 < width / 2 ? 'end' : 'start')
        .attr('font-family', "'Plus Jakarta Sans', sans-serif")
        .attr('font-size', '11px')
        .attr('font-weight', '700')
        .attr('fill', c.textPrimary)
        .text(d => `${d.name} (${formatNumber(d.value, 2)} jt ton)`);
}

function updateBalanceTheme() {
    const c = getThemeColors();

    if (waterfallChart) {
        waterfallChart.updateOptions({
            theme: { mode: c.isDark ? 'dark' : 'light' },
            grid: { borderColor: c.gridLine },
            xaxis: { labels: { style: { colors: c.textSecondary } } },
            yaxis: { title: { style: { color: c.textSecondary } }, labels: { style: { colors: c.textSecondary } } }
        });
    }

    if (radarChart) {
        radarChart.options.scales.r.angleLines.color = c.gridLine;
        radarChart.options.scales.r.grid.color = c.gridLine;
        radarChart.options.scales.r.pointLabels.color = c.textPrimary;
        radarChart.options.scales.r.ticks.color = c.textSecondary;
        radarChart.options.plugins.legend.labels.color = c.textSecondary;
        radarChart.update();
    }

    renderD3Sankey();
}
