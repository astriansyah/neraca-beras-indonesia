/**
 * Modul Tema & Utilitas Chart Terpusat - Neraca Beras Indonesia 2024–2025
 * Mengintegrasikan palet warna, tipografi, format id-ID, dan bayangan halus (soft shadow)
 * untuk Chart.js, ApexCharts, dan D3.js.
 */

// =============================================================================
// PALET WARNA & TIPOGRAFI
// =============================================================================
export const PALETTE = {
    emerald: '#10b981', // Produksi Beras / Hijau Padi Utama
    emeraldDark: '#047857',
    emeraldLight: '#34d399',
    amber: '#f59e0b', // Gabah Kering / Konsumsi
    amberDark: '#d97706',
    amberLight: '#fbbf24',
    sky: '#0284c7', // Impor / Air
    skyDark: '#0369a1',
    skyLight: '#38bdf8',
    purple: '#8b5cf6', // Stok Bulog
    purpleDark: '#6d28d9',
    purpleLight: '#a78bfa',
    rose: '#ef4444', // Ekspor / Defisit
    slate: '#64748b', // Residual / Baseline
    slateDark: '#334155',
    slateLight: '#94a3b8',
};

export const FONT_FAMILY = "'Plus Jakarta Sans', system-ui, -apple-system, sans-serif";

/**
 * Cek apakah halaman sedang dalam mode gelap
 */
export function isDarkMode() {
    return document.documentElement.classList.contains('dark');
}

/**
 * Warna kontekstual berdasarkan tema (terang / gelap)
 */
export function getThemeColors() {
    const dark = isDarkMode();
    return {
        isDark: dark,
        textPrimary: dark ? '#f8fafc' : '#0f172a',
        textSecondary: dark ? '#94a3b8' : '#64748b',
        textMuted: dark ? '#64748b' : '#94a3b8',
        gridLine: dark ? 'rgba(51, 65, 85, 0.4)' : 'rgba(241, 245, 249, 0.9)',
        cardBg: dark ? '#0f172a' : '#ffffff',
        tooltipBg: dark ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.95)',
        tooltipBorder: dark ? 'rgba(51, 65, 85, 0.8)' : 'rgba(226, 232, 240, 0.9)',
        shadowColor: dark ? 'rgba(0, 0, 0, 0.5)' : 'rgba(16, 185, 129, 0.22)',
    };
}

// =============================================================================
// FORMATTER ANGKA LOKAL INDONESIA (id-ID)
// =============================================================================

export function formatNumber(val, decimals = 2) {
    if (val === null || val === undefined || isNaN(val)) return '-';
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: decimals,
    }).format(val);
}

export function formatPercent(val, decimals = 1) {
    if (val === null || val === undefined || isNaN(val)) return '-';
    return `${new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: decimals,
    }).format(val)}%`;
}

export function formatCurrency(val, currency = 'USD') {
    if (val === null || val === undefined || isNaN(val)) return '-';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: currency,
        maximumFractionDigits: 0,
    }).format(val);
}

// =============================================================================
// CHART.JS: PLUGIN SHADOW & DEFAULTS
// =============================================================================

/**
 * Plugin custom Chart.js untuk memberikan bayangan halus (soft shadow) pada bar/line
 */
export const chartJsShadowPlugin = {
    id: 'customShadow',
    beforeDatasetDraw(chart, args, options) {
        const { ctx } = chart;
        ctx.save();
        const colors = getThemeColors();
        ctx.shadowColor = options.shadowColor || colors.shadowColor;
        ctx.shadowBlur = options.shadowBlur ?? 14;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = options.shadowOffsetY ?? 6;
    },
    afterDatasetDraw(chart) {
        const { ctx } = chart;
        ctx.restore();
    }
};

/**
 * Konfigurasi dasar Chart.js yang konsisten dengan tema
 */
export function getChartJsDefaults() {
    const c = getThemeColors();
    return {
        responsive: true,
        maintainAspectRatio: false,
        font: {
            family: FONT_FAMILY,
        },
        plugins: {
            legend: {
                labels: {
                    color: c.textSecondary,
                    font: { family: FONT_FAMILY, size: 12, weight: '600' },
                    boxWidth: 12,
                    boxHeight: 12,
                    usePointStyle: true,
                    padding: 16,
                }
            },
            tooltip: {
                backgroundColor: c.tooltipBg,
                titleColor: c.textPrimary,
                bodyColor: c.textSecondary,
                borderColor: c.tooltipBorder,
                borderWidth: 1,
                padding: 12,
                boxPadding: 6,
                usePointStyle: true,
                titleFont: { family: FONT_FAMILY, weight: '700', size: 12 },
                bodyFont: { family: FONT_FAMILY, size: 12 },
                cornerRadius: 12,
                displayColors: true,
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    color: c.textSecondary,
                    font: { family: FONT_FAMILY, size: 11, weight: '500' }
                }
            },
            y: {
                grid: {
                    color: c.gridLine,
                    drawBorder: false,
                },
                ticks: {
                    color: c.textSecondary,
                    font: { family: FONT_FAMILY, size: 11 }
                }
            }
        }
    };
}

// =============================================================================
// APEXCHARTS: KONFIGURASI TEMA TERPUSAT
// =============================================================================

export function getApexThemeDefaults(options = {}) {
    const c = getThemeColors();
    const { chart = {}, stroke = {}, grid = {}, theme = {}, tooltip = {}, legend = {}, ...rest } = options;
    return {
        ...rest,
        chart: {
            fontFamily: FONT_FAMILY,
            toolbar: { show: false },
            dropShadow: {
                enabled: false,
                top: 4,
                left: 0,
                blur: 8,
                opacity: c.isDark ? 0.35 : 0.18,
                color: PALETTE.emerald,
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 700,
            },
            background: 'transparent',
            ...chart,
        },
        stroke: {
            curve: 'smooth',
            width: 3,
            ...stroke,
        },
        grid: {
            borderColor: c.gridLine,
            strokeDashArray: 3,
            ...grid,
        },
        theme: {
            mode: c.isDark ? 'dark' : 'light',
            ...theme,
        },
        tooltip: {
            theme: c.isDark ? 'dark' : 'light',
            style: { fontFamily: FONT_FAMILY, fontSize: '12px' },
            y: {
                formatter: (val) => formatNumber(val, 2),
            },
            ...tooltip,
        },
        legend: {
            fontFamily: FONT_FAMILY,
            labels: { colors: c.textSecondary },
            markers: { radius: 12 },
            ...legend,
        }
    };
}

// =============================================================================
// D3.JS: FILTER SHADOW & UTILITIES
// =============================================================================

/**
 * Sisipkan filter bayangan halus SVG untuk elemen D3
 */
export function appendD3DropShadowFilter(svg, id = 'd3-soft-shadow') {
    const defs = svg.select('defs').empty() ? svg.append('defs') : svg.select('defs');
    
    // Hapus filter lama jika ada
    defs.select(`#${id}`).remove();

    const filter = defs.append('filter')
        .attr('id', id)
        .attr('x', '-20%')
        .attr('y', '-20%')
        .attr('width', '140%')
        .attr('height', '140%');

    filter.append('feDropShadow')
        .attr('dx', '0')
        .attr('dy', '4')
        .attr('stdDeviation', '4')
        .attr('flood-color', PALETTE.emerald)
        .attr('flood-opacity', '0.22');

    return `url(#${id})`;
}

// =============================================================================
// PENGUNDUH GAMBAR PNG (Client-Side Chart Download)
// =============================================================================

export async function downloadChartAsPng(chartElementId, filename = 'chart') {
    const el = document.getElementById(chartElementId);
    if (!el) {
        console.warn(`Elemen #${chartElementId} tidak ditemukan untuk diunduh.`);
        return;
    }

    try {
        // 1. Jika elemen adalah / berisi canvas (Chart.js)
        const canvas = el.tagName.toLowerCase() === 'canvas' ? el : el.querySelector('canvas');
        if (canvas) {
            const dataUrl = canvas.toDataURL('image/png');
            triggerDownload(dataUrl, `${filename}.png`);
            return;
        }

        // 2. Jika elemen ApexCharts
        if (window.ApexCharts && el.querySelector('.apexcharts-canvas')) {
            const chartInstance = window.ApexCharts.getChartByID ? window.ApexCharts.getChartByID(chartElementId) : null;
            if (chartInstance && typeof chartInstance.dataURI === 'function') {
                const uri = await chartInstance.dataURI();
                if (uri && uri.imgURI) {
                    triggerDownload(uri.imgURI, `${filename}.png`);
                    return;
                }
            }
        }

        // 3. Jika elemen SVG (D3.js atau Apex fallback)
        const svg = el.querySelector('svg');
        if (svg) {
            const svgData = new XMLSerializer().serializeToString(svg);
            const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
            const URL = window.URL || window.webkitURL || window;
            const blobURL = URL.createObjectURL(svgBlob);
            
            const img = new Image();
            img.onload = () => {
                const offscreenCanvas = document.createElement('canvas');
                offscreenCanvas.width = svg.clientWidth || 800;
                offscreenCanvas.height = svg.clientHeight || 500;
                const ctx = offscreenCanvas.getContext('2d');
                ctx.fillStyle = isDarkMode() ? '#0f172a' : '#ffffff';
                ctx.fillRect(0, 0, offscreenCanvas.width, offscreenCanvas.height);
                ctx.drawImage(img, 0, 0);
                const pngUrl = offscreenCanvas.toDataURL('image/png');
                triggerDownload(pngUrl, `${filename}.png`);
                URL.revokeObjectURL(blobURL);
            };
            img.src = blobURL;
            return;
        }

        console.warn('Tidak dapat menemukan canvas atau SVG pada elemen untuk diekspor.');
    } catch (err) {
        console.error('Gagal mengunduh chart sebagai PNG:', err);
    }
}

function triggerDownload(dataUrl, filename) {
    const a = document.createElement('a');
    a.href = dataUrl;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Global registry untuk kemudahan integrasi dengan Blade x-data
if (typeof window !== 'undefined') {
    window.nbCharts = {
        downloadPng: downloadChartAsPng,
        palette: PALETTE,
        theme: getThemeColors,
        formatNumber,
        formatPercent,
    };
}
