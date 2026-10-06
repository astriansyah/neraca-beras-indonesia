import * as d3 from 'd3';
import ApexCharts from 'apexcharts';
import { getApexThemeDefaults, isDarkMode } from '../charts/theme';

/**
 * Generator Variabel Acak Distribusi Triangular
 * T(min, mode, max)
 */
function randomTriangular(min, mode, max) {
    const u = Math.random();
    const fc = (mode - min) / (max - min);
    if (u < fc) {
        return min + Math.sqrt(u * (max - min) * (mode - min));
    } else {
        return max - Math.sqrt((1 - u) * (max - min) * (max - mode));
    }
}

/**
 * Kernel Density Estimation (KDE) sederhana
 */
function kernelDensityEstimator(kernel, X) {
    return function(V) {
        return X.map(x => [x, d3.mean(V, v => kernel(x - v))]);
    };
}
function epanechnikov(bandwidth) {
    return function(x) {
        return Math.abs(x /= bandwidth) <= 1 ? 0.75 * (1 - x * x) / bandwidth : 0;
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const rawData = window.__NB_SIMULATION__ || {};
    const defaultParams = {
        gkg: 60.21,
        rendemen: 57.65,
        konsumsi: 31.10,
        impor: 0.45,
        stokAwal: 2.00,
    };

    let currentParams = { ...defaultParams };
    let cdfChartInstance = null;
    let sensitivityChartInstance = null;
    let lastSimResults = null;

    // DOM Elements
    const sliderGkg = document.getElementById('slider-gkg');
    const sliderRendemen = document.getElementById('slider-rendemen');
    const sliderKonsumsi = document.getElementById('slider-konsumsi');
    const sliderImpor = document.getElementById('slider-impor');
    const sliderStok = document.getElementById('slider-stok');

    const valGkg = document.getElementById('val-gkg');
    const valRendemen = document.getElementById('val-rendemen');
    const valKonsumsi = document.getElementById('val-konsumsi');
    const valImpor = document.getElementById('val-impor');
    const valStok = document.getElementById('val-stok');

    // Preset configurations
    const presets = {
        baseline: { gkg: 60.21, rendemen: 57.65, konsumsi: 31.10, impor: 0.45, stokAwal: 2.00 },
        optimistic: { gkg: 63.50, rendemen: 60.00, konsumsi: 30.00, impor: 0.00, stokAwal: 2.00 },
        pessimistic: { gkg: 54.00, rendemen: 55.00, konsumsi: 31.50, impor: 2.50, stokAwal: 2.00 },
        bps_low: { gkg: 60.21, rendemen: 57.65, konsumsi: 26.06, impor: 0.45, stokAwal: 2.00 },
    };

    function updateSliderDisplays() {
        if (sliderGkg) { sliderGkg.value = currentParams.gkg; valGkg.textContent = Number(currentParams.gkg).toLocaleString('id-ID', { minimumFractionDigits: 2 }); }
        if (sliderRendemen) { sliderRendemen.value = currentParams.rendemen; valRendemen.textContent = Number(currentParams.rendemen).toLocaleString('id-ID', { minimumFractionDigits: 2 }); }
        if (sliderKonsumsi) { sliderKonsumsi.value = currentParams.konsumsi; valKonsumsi.textContent = Number(currentParams.konsumsi).toLocaleString('id-ID', { minimumFractionDigits: 2 }); }
        if (sliderImpor) { sliderImpor.value = currentParams.impor; valImpor.textContent = Number(currentParams.impor).toLocaleString('id-ID', { minimumFractionDigits: 2 }); }
        if (sliderStok) { sliderStok.value = currentParams.stokAwal; valStok.textContent = Number(currentParams.stokAwal).toLocaleString('id-ID', { minimumFractionDigits: 2 }); }
    }

    /**
     * Jalankan 10.000 iterasi Monte Carlo
     */
    function executeMonteCarlo(params) {
        const N = 10000;
        const gkg = parseFloat(params.gkg);
        const rMode = parseFloat(params.rendemen);
        const rMin = Math.min(54.0, rMode - 2.65);
        const rMax = Math.max(62.0, rMode + 4.35);

        const cMode = parseFloat(params.konsumsi);
        const cMin = Math.min(26.06, cMode - 2.5);
        const cMax = Math.max(32.5, cMode + 1.2);

        const impor = parseFloat(params.impor);
        const ekspor = 0.00006;

        const surpluses = new Float64Array(N);
        const ssrs = new Float64Array(N);

        let sumSurplus = 0;
        let deficitCount = 0;
        let sumSSR = 0;

        for (let i = 0; i < N; i++) {
            const r = randomTriangular(rMin, rMode, rMax) / 100;
            const c = randomTriangular(cMin, cMode, cMax);
            const prodBeras = gkg * r;
            const surplus = prodBeras + impor - ekspor - c;
            const denom = prodBeras + impor - ekspor;
            const ssr = denom > 0 ? (prodBeras / denom) * 100 : 100;

            surpluses[i] = surplus;
            ssrs[i] = ssr;

            sumSurplus += surplus;
            if (surplus < 0) deficitCount++;
            sumSSR += ssr;
        }

        surpluses.sort(); // Sort in place

        const mean = sumSurplus / N;
        let variance = 0;
        for (let i = 0; i < N; i++) {
            const diff = surpluses[i] - mean;
            variance += diff * diff;
        }
        const std = Math.sqrt(variance / N);

        const p0 = surpluses[0];
        const p5 = surpluses[Math.floor(N * 0.05)];
        const p10 = surpluses[Math.floor(N * 0.10)];
        const p25 = surpluses[Math.floor(N * 0.25)];
        const p50 = surpluses[Math.floor(N * 0.50)];
        const p75 = surpluses[Math.floor(N * 0.75)];
        const p90 = surpluses[Math.floor(N * 0.90)];
        const p95 = surpluses[Math.floor(N * 0.95)];
        const p100 = surpluses[N - 1];

        const probDeficit = (deficitCount / N) * 100;
        const probSurplus = 100 - probDeficit;
        const meanSSR = sumSSR / N;

        lastSimResults = {
            surpluses,
            N,
            mean,
            median: p50,
            std,
            p0, p5, p10, p25, p50, p75, p90, p95, p100,
            probDeficit,
            probSurplus,
            meanSSR,
            params: { ...params },
        };

        updateKPIs(lastSimResults);
        updatePercentileTable(lastSimResults);
        renderD3Histogram(lastSimResults);
        renderCDFChart(lastSimResults);
        renderSensitivityChart(params);
    }

    /**
     * Perbarui Kartu KPI
     */
    function updateKPIs(res) {
        const kpiMean = document.getElementById('kpi-sim-mean');
        const kpiP50 = document.getElementById('kpi-sim-p50');
        const kpiProbSurplus = document.getElementById('kpi-sim-prob-surplus');
        const kpiProbDeficit = document.getElementById('kpi-sim-prob-deficit');
        const kpiCI = document.getElementById('kpi-sim-ci');
        const kpiSSR = document.getElementById('kpi-sim-ssr');

        if (kpiMean) {
            const prefix = res.mean >= 0 ? '+' : '';
            kpiMean.textContent = `${prefix}${res.mean.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} jt ton`;
            kpiMean.className = `mt-1 text-2xl font-extrabold tracking-tight tabular-nums ${res.mean >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'}`;
        }
        if (kpiP50) {
            const prefix = res.median >= 0 ? '+' : '';
            kpiP50.textContent = `${prefix}${res.median.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} jt ton`;
        }
        if (kpiProbSurplus) {
            kpiProbSurplus.textContent = `${res.probSurplus.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}%`;
            kpiProbSurplus.className = `mt-1 text-2xl font-extrabold tracking-tight tabular-nums ${res.probSurplus >= 90 ? 'text-emerald-600 dark:text-emerald-400' : (res.probSurplus >= 70 ? 'text-amber-500' : 'text-rose-600')}`;
        }
        if (kpiProbDeficit) {
            kpiProbDeficit.textContent = `P(Defisit) = ${res.probDeficit.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}%`;
            kpiProbDeficit.className = `mt-1 inline-flex items-center gap-1 text-[11px] font-semibold ${res.probDeficit > 5 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-500 dark:text-slate-400'}`;
        }
        if (kpiCI) {
            const p5Str = `${res.p5 >= 0 ? '+' : ''}${res.p5.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            const p95Str = `${res.p95 >= 0 ? '+' : ''}${res.p95.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            kpiCI.textContent = `${p5Str} s/d ${p95Str} jt ton`;
        }
        if (kpiSSR) {
            kpiSSR.textContent = `${res.meanSSR.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}%`;
        }
    }

    /**
     * Perbarui Tabel Persentil
     */
    function updatePercentileTable(res) {
        const fmt = (v) => `${v >= 0 ? '+' : ''}${v.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        };

        setVal('stat-p0', fmt(res.p0));
        setVal('stat-p5', fmt(res.p5));
        setVal('stat-p25', fmt(res.p25));
        setVal('stat-p50', fmt(res.p50));
        setVal('stat-mean', fmt(res.mean));
        setVal('stat-p75', fmt(res.p75));
        setVal('stat-p95', fmt(res.p95));
        setVal('stat-p100', fmt(res.p100));
        setVal('stat-std', res.std.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    /**
     * Render D3.js Histogram & Kurva Densitas KDE
     */
    function renderD3Histogram(res) {
        const container = document.getElementById('chart-sim-histogram');
        if (!container) return;
        container.innerHTML = '';

        const width = container.clientWidth || 650;
        const height = 340;
        const margin = { top: 25, right: 30, bottom: 45, left: 50 };

        const svg = d3.select(container)
            .append('svg')
            .attr('viewBox', `0 0 ${width} ${height}`)
            .attr('width', '100%')
            .attr('height', height)
            .style('overflow', 'visible');

        const dark = isDarkMode();
        const data = Array.from(res.surpluses);

        // X scale
        const minX = Math.min(-1.5, d3.min(data) * 1.05);
        const maxX = Math.max(6.0, d3.max(data) * 1.05);

        const x = d3.scaleLinear()
            .domain([minX, maxX])
            .range([margin.left, width - margin.right]);

        // Histogram bins
        const bins = d3.bin()
            .domain(x.domain())
            .thresholds(x.ticks(45))(data);

        // Y scale
        const maxFreq = d3.max(bins, d => d.length) || 100;
        const y = d3.scaleLinear()
            .domain([0, maxFreq * 1.15])
            .range([height - margin.bottom, margin.top]);

        // Defs for gradients & shadow
        const defs = svg.append('defs');

        // Drop shadow filter
        const filter = defs.append('filter')
            .attr('id', 'hist-bar-shadow')
            .attr('x', '-5%').attr('y', '-5%').attr('width', '110%').attr('height', '120%');
        filter.append('feDropShadow')
            .attr('dx', 0)
            .attr('dy', 2)
            .attr('stdDeviation', 2)
            .attr('flood-opacity', dark ? '0.35' : '0.12');

        // Tooltip element
        let tooltip = d3.select('#d3-hist-tooltip');
        if (tooltip.empty()) {
            tooltip = d3.select('body').append('div')
                .attr('id', 'd3-hist-tooltip')
                .attr('class', 'nb-tooltip opacity-0')
                .style('display', 'none')
                .style('position', 'absolute')
                .style('pointer-events', 'none')
                .style('z-index', '9999');
        }

        // Draw Bars
        svg.append('g')
            .selectAll('rect')
            .data(bins)
            .join('rect')
            .attr('x', d => x(d.x0) + 1)
            .attr('width', d => Math.max(0, x(d.x1) - x(d.x0) - 1.5))
            .attr('y', d => y(d.length))
            .attr('height', d => y(0) - y(d.length))
            .attr('rx', 2)
            .attr('fill', d => {
                const mid = (d.x0 + d.x1) / 2;
                return mid < 0 ? '#f43f5e' : (dark ? '#10b981' : '#059669');
            })
            .attr('fill-opacity', dark ? 0.75 : 0.85)
            .attr('filter', 'url(#hist-bar-shadow)')
            .style('cursor', 'pointer')
            .on('mouseenter', function(event, d) {
                d3.select(this).attr('fill-opacity', 1).attr('stroke', '#fff').attr('stroke-width', 1.5);
                const pct = ((d.length / res.N) * 100).toFixed(2);
                tooltip.style('display', 'block')
                    .classed('opacity-0', false)
                    .html(`
                        <div class="t-title">Rentang Neraca: ${d.x0.toFixed(2)} s/d ${d.x1.toFixed(2)} jt ton</div>
                        <div class="t-row"><span class="flex items-center"><span class="t-dot" style="background:${(d.x0+d.x1)/2 < 0 ? '#f43f5e' : '#10b981'}"></span>Jumlah Iterasi</span><span class="t-val">${d.length.toLocaleString('id-ID')}</span></div>
                        <div class="t-row"><span class="flex items-center"><span class="t-dot" style="background:#0284c7"></span>Frekuensi Relatif</span><span class="t-val">${pct}%</span></div>
                    `);
            })
            .on('mousemove', function(event) {
                tooltip.style('left', `${event.pageX + 15}px`).style('top', `${event.pageY - 40}px`);
            })
            .on('mouseleave', function() {
                d3.select(this).attr('fill-opacity', dark ? 0.75 : 0.85).attr('stroke', null);
                tooltip.classed('opacity-0', true).style('display', 'none');
            });

        // Compute Kernel Density Estimation (KDE)
        const kde = kernelDensityEstimator(epanechnikov(0.35), x.ticks(100));
        const density = kde(data);
        const maxDensity = d3.max(density, d => d[1]) || 1;
        const yDensity = d3.scaleLinear()
            .domain([0, maxDensity])
            .range([height - margin.bottom, margin.top]);

        // Draw KDE density curve
        const line = d3.line()
            .curve(d3.curveBasis)
            .x(d => x(d[0]))
            .y(d => yDensity(d[1]));

        svg.append('path')
            .datum(density)
            .attr('fill', 'none')
            .attr('stroke', dark ? '#34d399' : '#047857')
            .attr('stroke-width', 2.5)
            .attr('d', line);

        // Reference Lines: 0, P5, P50 (Median), P95
        const linesData = [
            { val: 0, color: '#f43f5e', dash: '3,3', label: 'Impas 0' },
            { val: res.p5, color: '#f59e0b', dash: '4,4', label: `P5: ${res.p5.toFixed(2)}` },
            { val: res.median, color: '#0ea5e9', dash: '4,4', label: `Median: ${res.median.toFixed(2)}` },
            { val: res.p95, color: '#a855f7', dash: '4,4', label: `P95: ${res.p95.toFixed(2)}` },
        ];

        linesData.forEach(lineItem => {
            const lx = x(lineItem.val);
            if (lx >= margin.left && lx <= width - margin.right) {
                svg.append('line')
                    .attr('x1', lx).attr('x2', lx)
                    .attr('y1', margin.top - 8).attr('y2', height - margin.bottom)
                    .attr('stroke', lineItem.color)
                    .attr('stroke-width', lineItem.val === 0 ? 2 : 1.5)
                    .attr('stroke-dasharray', lineItem.dash);

                svg.append('text')
                    .attr('x', lx)
                    .attr('y', margin.top - 12)
                    .attr('text-anchor', 'middle')
                    .attr('font-size', '10px')
                    .attr('font-weight', '700')
                    .attr('fill', lineItem.color)
                    .text(lineItem.label);
            }
        });

        // X Axis
        svg.append('g')
            .attr('transform', `translate(0,${height - margin.bottom})`)
            .call(d3.axisBottom(x).ticks(8).tickFormat(d => `${d > 0 ? '+' : ''}${d} jt ton`))
            .call(g => g.select('.domain').attr('stroke', dark ? '#334155' : '#cbd5e1'))
            .call(g => g.selectAll('.tick line').attr('stroke', dark ? '#334155' : '#cbd5e1'))
            .call(g => g.selectAll('.tick text').attr('fill', dark ? '#94a3b8' : '#64748b').attr('font-size', '11px'));

        // Y Axis
        svg.append('g')
            .attr('transform', `translate(${margin.left},0)`)
            .call(d3.axisLeft(y).ticks(5).tickFormat(d3.format(',')))
            .call(g => g.select('.domain').remove())
            .call(g => g.selectAll('.tick line').attr('stroke', dark ? '#334155' : '#e2e8f0').attr('x2', width - margin.left - margin.right))
            .call(g => g.selectAll('.tick text').attr('fill', dark ? '#94a3b8' : '#64748b').attr('font-size', '11px'));
    }

    /**
     * Render ApexCharts CDF (Cumulative Distribution Function)
     */
    function renderCDFChart(res) {
        const container = document.getElementById('chart-sim-cdf');
        if (!container) return;

        // Sample 80 points along the CDF
        const points = [];
        const step = Math.floor(res.N / 80);
        for (let i = 0; i < res.N; i += step) {
            const surplusVal = Number(res.surpluses[i].toFixed(2));
            const cdfProb = Number(((i / res.N) * 100).toFixed(1));
            points.push({ x: surplusVal, y: cdfProb });
        }
        // Add end point
        points.push({ x: Number(res.surpluses[res.N - 1].toFixed(2)), y: 100 });

        const dark = isDarkMode();
        const base = getApexThemeDefaults();

        const options = {
            ...base,
            chart: {
                type: 'line',
                height: 320,
                toolbar: { show: false },
                animations: { enabled: true, speed: 400 },
            },
            series: [{
                name: 'Probabilitas Kumulatif P(X ≤ x)',
                data: points,
            }],
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: [dark ? '#38bdf8' : '#0284c7'],
            },
            xaxis: {
                type: 'numeric',
                title: {
                    text: 'Saldo Neraca Bersih (Juta Ton)',
                    style: { fontSize: '11px', fontWeight: 600, color: dark ? '#94a3b8' : '#64748b' },
                },
                labels: {
                    formatter: (val) => val != null ? `${val > 0 ? '+' : ''}${val.toFixed(1)}` : '',
                    style: { colors: dark ? '#94a3b8' : '#64748b', fontSize: '11px' },
                },
            },
            yaxis: {
                min: 0,
                max: 100,
                title: {
                    text: 'Probabilitas Kumulatif (%)',
                    style: { fontSize: '11px', fontWeight: 600, color: dark ? '#94a3b8' : '#64748b' },
                },
                labels: {
                    formatter: (val) => `${val}%`,
                    style: { colors: dark ? '#94a3b8' : '#64748b', fontSize: '11px' },
                },
            },
            annotations: {
                xaxis: [{
                    x: 0,
                    borderColor: '#f43f5e',
                    strokeDashArray: 3,
                    label: {
                        borderColor: '#f43f5e',
                        style: { color: '#fff', background: '#f43f5e', fontSize: '10px', fontWeight: 700 },
                        text: `Ambang Defisit 0 (P = ${res.probDeficit.toFixed(1)}%)`,
                    },
                }],
                yaxis: [{
                    y: 50,
                    borderColor: '#10b981',
                    strokeDashArray: 4,
                    label: {
                        borderColor: '#10b981',
                        style: { color: '#fff', background: '#10b981', fontSize: '10px', fontWeight: 700 },
                        text: 'Median P50 (50%)',
                    },
                }],
            },
            tooltip: {
                custom: function({ dataPointIndex, w }) {
                    const d = points[dataPointIndex];
                    if (!d) return '';
                    return `
                        <div class="nb-tooltip">
                            <div class="t-title">Titik Neraca: ${d.x > 0 ? '+' : ''}${d.x} jt ton</div>
                            <div class="t-row"><span class="flex items-center"><span class="t-dot" style="background:#0284c7"></span>P(X &le; x)</span><span class="t-val">${d.y}%</span></div>
                            <div class="t-row"><span class="flex items-center"><span class="t-dot" style="background:#10b981"></span>P(X &gt; x)</span><span class="t-val">${(100 - d.y).toFixed(1)}%</span></div>
                        </div>
                    `;
                },
            },
        };

        if (cdfChartInstance) {
            cdfChartInstance.destroy();
        }
        cdfChartInstance = new ApexCharts(container, options);
        cdfChartInstance.render();
    }

    /**
     * Render Diagram Sensitivitas Tornado ApexCharts
     */
    function renderSensitivityChart(params) {
        const container = document.getElementById('chart-sim-sensitivity');
        if (!container) return;

        // Hitung dampak variasi +-5% pada 4 parameter
        const baseGkg = parseFloat(params.gkg);
        const baseRendemen = parseFloat(params.rendemen);
        const baseKonsumsi = parseFloat(params.konsumsi);
        const baseImpor = parseFloat(params.impor);

        // 1. Rendemen (+-5% relatif = +-2.88 poin persentase)
        const rDelta = (baseRendemen * 0.05) / 100;
        const impactRendemenPos = baseGkg * rDelta;
        const impactRendemenNeg = -impactRendemenPos;

        // 2. Produksi GKG (+-5% = +-3.01 juta ton GKG)
        const gkgDelta = baseGkg * 0.05;
        const impactGkgPos = gkgDelta * (baseRendemen / 100);
        const impactGkgNeg = -impactGkgPos;

        // 3. Konsumsi (+-5% = -+1.55 jt ton ke surplus)
        const cDelta = baseKonsumsi * 0.05;
        const impactKonsumsiPos = -cDelta; // kenaikan konsumsi menurunkan surplus
        const impactKonsumsiNeg = cDelta;  // penurunan konsumsi menaikkan surplus

        // 4. Kuota Impor (+-5% dari impor baseline atau minimal 0.2 jt ton)
        const imporDelta = Math.max(0.2, baseImpor * 0.5);
        const impactImporPos = imporDelta;
        const impactImporNeg = -imporDelta;

        const dark = isDarkMode();
        const base = getApexThemeDefaults();

        const categories = [
            'Rendemen Beras (±5% Relatif)',
            'Produksi GKG (±5%)',
            'Kebutuhan Konsumsi (±5%)',
            'Penambahan Kuota Impor',
        ];

        const options = {
            ...base,
            chart: {
                type: 'bar',
                height: 320,
                stacked: true,
                toolbar: { show: false },
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '48%',
                    borderRadius: 4,
                },
            },
            colors: ['#f43f5e', '#10b981'],
            series: [
                {
                    name: 'Dampak Penurunan Variabel (-5%)',
                    data: [
                        Number(impactRendemenNeg.toFixed(2)),
                        Number(impactGkgNeg.toFixed(2)),
                        Number(impactKonsumsiNeg.toFixed(2)),
                        Number(impactImporNeg.toFixed(2)),
                    ],
                },
                {
                    name: 'Dampak Kenaikan Variabel (+5%)',
                    data: [
                        Number(impactRendemenPos.toFixed(2)),
                        Number(impactGkgPos.toFixed(2)),
                        Number(impactKonsumsiPos.toFixed(2)),
                        Number(impactImporPos.toFixed(2)),
                    ],
                },
            ],
            xaxis: {
                title: {
                    text: 'Dampak terhadap Saldo Neraca (Juta Ton)',
                    style: { fontSize: '11px', fontWeight: 600, color: dark ? '#94a3b8' : '#64748b' },
                },
                labels: {
                    formatter: (val) => `${val > 0 ? '+' : ''}${val} jt ton`,
                    style: { colors: dark ? '#94a3b8' : '#64748b', fontSize: '11px' },
                },
            },
            yaxis: {
                labels: {
                    style: { colors: dark ? '#cbd5e1' : '#334155', fontSize: '11px', fontWeight: 600 },
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'center',
                labels: { colors: dark ? '#cbd5e1' : '#334155' },
            },
            tooltip: {
                custom: function({ series, seriesIndex, dataPointIndex }) {
                    const val = series[seriesIndex][dataPointIndex];
                    const cat = categories[dataPointIndex];
                    return `
                        <div class="nb-tooltip">
                            <div class="t-title">${cat}</div>
                            <div class="t-row"><span class="flex items-center"><span class="t-dot" style="background:${seriesIndex === 0 ? '#f43f5e' : '#10b981'}"></span>${seriesIndex === 0 ? 'Dampak Negatif' : 'Dampak Positif'}</span><span class="t-val">${val > 0 ? '+' : ''}${val} jt ton</span></div>
                        </div>
                    `;
                },
            },
        };

        if (sensitivityChartInstance) {
            sensitivityChartInstance.destroy();
        }
        sensitivityChartInstance = new ApexCharts(container, options);
        sensitivityChartInstance.render();
    }

    // Slider Event Listeners with Debounce
    let debounceTimer = null;
    function handleSliderChange() {
        currentParams.gkg = parseFloat(sliderGkg.value);
        currentParams.rendemen = parseFloat(sliderRendemen.value);
        currentParams.konsumsi = parseFloat(sliderKonsumsi.value);
        currentParams.impor = parseFloat(sliderImpor.value);
        currentParams.stokAwal = parseFloat(sliderStok.value);

        updateSliderDisplays();

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            executeMonteCarlo(currentParams);
        }, 150);
    }

    [sliderGkg, sliderRendemen, sliderKonsumsi, sliderImpor, sliderStok].forEach(slider => {
        if (slider) {
            slider.addEventListener('input', handleSliderChange);
        }
    });

    // Preset Buttons
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const presetKey = btn.getAttribute('data-preset');
            if (presets[presetKey]) {
                currentParams = { ...presets[presetKey] };
                updateSliderDisplays();
                executeMonteCarlo(currentParams);

                // Highlight active preset button
                document.querySelectorAll('.preset-btn').forEach(b => {
                    b.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50/70');
                });
                btn.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50/70');
            }
        });
    });

    // Reset button
    const btnReset = document.getElementById('btn-reset-sim');
    if (btnReset) {
        btnReset.addEventListener('click', () => {
            currentParams = { ...defaultParams };
            updateSliderDisplays();
            executeMonteCarlo(currentParams);
        });
    }

    // Re-run simulation button
    const btnReRun = document.getElementById('btn-re-run');
    if (btnReRun) {
        btnReRun.addEventListener('click', () => {
            executeMonteCarlo(currentParams);
        });
    }

    // Export CSV (1000 sample points)
    const btnExportCsv = document.getElementById('btn-export-sim-csv');
    if (btnExportCsv) {
        btnExportCsv.addEventListener('click', () => {
            if (!lastSimResults) return;
            let csv = 'Iterasi,Surplus_Juta_Ton\n';
            const step = Math.floor(lastSimResults.N / 1000);
            for (let i = 0; i < lastSimResults.N; i += step) {
                csv += `${i + 1},${lastSimResults.surpluses[i].toFixed(4)}\n`;
            }
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `simulasi_monte_carlo_neraca_beras_${Date.now()}.csv`;
            a.click();
            URL.revokeObjectURL(url);
        });
    }

    // Export JSON Summary
    const btnExportJson = document.getElementById('btn-export-sim-json');
    if (btnExportJson) {
        btnExportJson.addEventListener('click', () => {
            if (!lastSimResults) return;
            const summary = {
                metadata: {
                    title: 'Ringkasan Simulasi Monte Carlo Neraca Beras 2025',
                    iterations: lastSimResults.N,
                    timestamp: new Date().toISOString(),
                    parameters: lastSimResults.params,
                },
                statistics: {
                    mean: lastSimResults.mean,
                    median: lastSimResults.median,
                    std_dev: lastSimResults.std,
                    p0_min: lastSimResults.p0,
                    p5: lastSimResults.p5,
                    p10: lastSimResults.p10,
                    p25: lastSimResults.p25,
                    p50: lastSimResults.p50,
                    p75: lastSimResults.p75,
                    p90: lastSimResults.p90,
                    p95: lastSimResults.p95,
                    p100_max: lastSimResults.p100,
                    prob_deficit_pct: lastSimResults.probDeficit,
                    prob_surplus_pct: lastSimResults.probSurplus,
                    mean_ssr_pct: lastSimResults.meanSSR,
                },
            };
            const blob = new Blob([JSON.stringify(summary, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `ringkasan_simulasi_neraca_beras_${Date.now()}.json`;
            a.click();
            URL.revokeObjectURL(url);
        });
    }

    // Responsive redraw
    window.addEventListener('resize', () => {
        if (lastSimResults) {
            renderD3Histogram(lastSimResults);
        }
    });

    // Theme change listener
    const themeObserver = new MutationObserver(() => {
        if (lastSimResults) {
            renderD3Histogram(lastSimResults);
            renderCDFChart(lastSimResults);
            renderSensitivityChart(currentParams);
        }
    });
    themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

    // Initial run
    updateSliderDisplays();
    executeMonteCarlo(currentParams);
});
