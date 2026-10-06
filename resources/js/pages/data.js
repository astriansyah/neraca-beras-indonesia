/**
 * Entry JS untuk halaman Data & Sumber (/data)
 * Mengelola tabel data interaktif: pencarian, filter, pagination, modal, dan ekspor.
 */
document.addEventListener('DOMContentLoaded', () => {
    const rawDataPoints = window.__NB_DATA_POINTS__ || [];
    const rawSources = window.__NB_SOURCES__ || [];

    // Normalisasi data untuk kemudahan filtering
    const items = rawDataPoints.map((dp, idx) => {
        const catName = dp.indicator?.category?.name || 'Umum';
        const catCode = dp.indicator?.category?.code || '';
        const indName = dp.indicator?.name || 'Indikator';
        const indCode = dp.indicator?.code || '';
        const unit = dp.indicator?.unit || '';
        const val = dp.nilai !== null ? dp.nilai : (dp.nilai_min !== null && dp.nilai_maks !== null ? `${dp.nilai_min} – ${dp.nilai_maks}` : '-');
        const numVal = dp.nilai !== null ? dp.nilai : dp.nilai_min;
        const isEst = Boolean(dp.is_estimate);
        const year = dp.tahun;
        const period = dp.periode || '';
        const notes = dp.catatan || '';
        const sources = dp.sources || [];
        const sourceTitles = sources.map(s => s.publisher || s.title).join(', ');

        return {
            id: dp.id || idx + 1,
            no: idx + 1,
            category: catName,
            categoryCode: catCode.toLowerCase(),
            indicator: indName,
            indicatorCode: indCode,
            unit: unit,
            val: val,
            numVal: numVal,
            isEstimate: isEst,
            year: year,
            period: period,
            notes: notes,
            sources: sources,
            sourceTitles: sourceTitles || 'BPS RI',
            raw: dp,
        };
    });

    // State
    let filteredItems = [...items];
    let currentPage = 1;
    let perPage = 25; // default 25

    // DOM Elements
    const searchInput = document.getElementById('data-search-input');
    const filterCat = document.getElementById('filter-category');
    const filterYear = document.getElementById('filter-year');
    const filterStatus = document.getElementById('filter-status');
    const perPageSelect = document.getElementById('data-per-page');

    const tableBody = document.getElementById('data-table-body');
    const showingCountEl = document.getElementById('data-showing-count');
    const totalCountEl = document.getElementById('data-total-count');
    const pageIndicator = document.getElementById('page-indicator');
    const btnPrev = document.getElementById('btn-page-prev');
    const btnNext = document.getElementById('btn-page-next');

    // Modal elements
    const modal = document.getElementById('data-detail-modal');
    const modalCard = document.getElementById('data-modal-card');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const btnCloseModalFooter = document.getElementById('btn-close-modal-footer');
    const modalCategoryBadge = document.getElementById('modal-category-badge');
    const modalTitle = document.getElementById('modal-indicator-title');
    const modalYear = document.getElementById('modal-year');
    const modalValue = document.getElementById('modal-value');
    const modalStatusDesc = document.getElementById('modal-status-desc');
    const modalNotes = document.getElementById('modal-notes');
    const modalSourcesList = document.getElementById('modal-sources-list');

    // Toast element
    const toast = document.getElementById('data-toast');
    const toastMsg = document.getElementById('toast-message');

    function showToast(message) {
        if (!toast) return;
        if (toastMsg) toastMsg.textContent = message;
        toast.classList.remove('opacity-0', 'pointer-events-none');
        setTimeout(() => {
            toast.classList.add('opacity-0', 'pointer-events-none');
        }, 2500);
    }

    function getCategoryBadge(cat) {
        const c = (cat || '').toLowerCase();
        if (c.includes('produksi')) {
            return `<span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/20">Produksi</span>`;
        }
        if (c.includes('konsumsi')) {
            return `<span class="inline-flex items-center rounded-md bg-sky-50 px-2 py-0.5 text-[10px] font-bold text-sky-700 ring-1 ring-sky-200 dark:bg-sky-500/10 dark:text-sky-300 dark:ring-sky-500/20">Konsumsi</span>`;
        }
        if (c.includes('perdagangan') || c.includes('impor') || c.includes('ekspor')) {
            return `<span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 ring-1 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/20">Perdagangan</span>`;
        }
        if (c.includes('stok')) {
            return `<span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700 ring-1 ring-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-300 dark:ring-indigo-500/20">Stok Bulog</span>`;
        }
        return `<span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-0.5 text-[10px] font-bold text-purple-700 ring-1 ring-purple-200 dark:bg-purple-500/10 dark:text-purple-300 dark:ring-purple-500/20">Neraca</span>`;
    }

    function getStatusBadge(isEst) {
        if (!isEst) {
            return `<span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                <span class="size-1.5 rounded-full bg-emerald-500"></span> Realisasi
            </span>`;
        }
        return `<span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">
            <span class="size-1.5 rounded-full bg-amber-500"></span> Estimasi
        </span>`;
    }

    function formatNumberId(val) {
        if (typeof val === 'number') {
            return val.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 4 });
        }
        return val;
    }

    /**
     * Filter data sesuai input pengguna
     */
    function applyFilter() {
        const query = (searchInput?.value || '').trim().toLowerCase();
        const selectedCat = (filterCat?.value || '').toLowerCase();
        const selectedYear = filterYear?.value || '';
        const selectedStatus = filterStatus?.value || '';

        filteredItems = items.filter(item => {
            // Text Search
            if (query) {
                const matchName = item.indicator.toLowerCase().includes(query);
                const matchCat = item.category.toLowerCase().includes(query);
                const matchNotes = item.notes.toLowerCase().includes(query);
                const matchSrc = item.sourceTitles.toLowerCase().includes(query);
                const matchVal = String(item.val).toLowerCase().includes(query);
                if (!matchName && !matchCat && !matchNotes && !matchSrc && !matchVal) {
                    return false;
                }
            }

            // Category Filter
            if (selectedCat) {
                const itemCat = item.category.toLowerCase();
                if (!itemCat.includes(selectedCat)) {
                    return false;
                }
            }

            // Year Filter
            if (selectedYear) {
                if (String(item.year) !== selectedYear) {
                    return false;
                }
            }

            // Status Filter
            if (selectedStatus === 'realisasi' && item.isEstimate) return false;
            if (selectedStatus === 'estimasi' && !item.isEstimate) return false;

            return true;
        });

        currentPage = 1;
        renderTable();
    }

    /**
     * Render Tabel dan Pagination
     */
    function renderTable() {
        if (!tableBody) return;

        const totalFiltered = filteredItems.length;
        const totalRows = items.length;

        if (showingCountEl) showingCountEl.textContent = totalFiltered;
        if (totalCountEl) totalCountEl.textContent = totalRows;

        // Compute Pagination
        const effectivePerPage = perPage === 'all' ? totalFiltered : parseInt(perPage, 10);
        const totalPages = effectivePerPage > 0 ? Math.max(1, Math.ceil(totalFiltered / effectivePerPage)) : 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        if (pageIndicator) pageIndicator.textContent = `${currentPage} / ${totalPages}`;
        if (btnPrev) btnPrev.disabled = currentPage <= 1;
        if (btnNext) btnNext.disabled = currentPage >= totalPages;

        const startIndex = (currentPage - 1) * effectivePerPage;
        const endIndex = perPage === 'all' ? totalFiltered : Math.min(startIndex + effectivePerPage, totalFiltered);
        const pageItems = filteredItems.slice(startIndex, endIndex);

        if (pageItems.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="p-8 text-center text-slate-400 dark:text-slate-500">
                        Tidak ada titik data yang cocok dengan kriteria pencarian/filter.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        pageItems.forEach((item, idx) => {
            const rowNumber = startIndex + idx + 1;
            const primarySource = item.sources[0]?.publisher || item.sourceTitles.split(',')[0] || 'BPS RI';
            const sourceUrl = item.sources[0]?.url || item.raw.sumber_url;

            html += `
                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                    <td class="text-center font-mono text-[11px] text-slate-400">${rowNumber}</td>
                    <td>${getCategoryBadge(item.category)}</td>
                    <td>
                        <span class="font-bold text-slate-900 dark:text-white block text-xs">${item.indicator}</span>
                        ${item.notes ? `<span class="text-[10px] text-slate-400 block line-clamp-1 mt-0.5">${item.notes}</span>` : ''}
                    </td>
                    <td class="text-center font-mono text-xs font-semibold text-slate-700 dark:text-slate-300">${item.year}</td>
                    <td class="num font-mono text-xs font-extrabold text-slate-900 dark:text-white">${formatNumberId(item.val)}</td>
                    <td class="text-xs text-slate-500 dark:text-slate-400">${item.unit}</td>
                    <td>${getStatusBadge(item.isEstimate)}</td>
                    <td class="text-xs">
                        ${sourceUrl ? `
                            <a href="${sourceUrl}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300">
                                ${primarySource} <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                        ` : `<span class="text-slate-500 dark:text-slate-400">${primarySource}</span>`}
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn-detail-row icon-btn size-7 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400" data-idx="${item.id}" title="Lihat Detail Indikator">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        </button>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;

        // Attach modal triggers
        tableBody.querySelectorAll('.btn-detail-row').forEach(btn => {
            btn.addEventListener('click', () => {
                const itemId = parseInt(btn.getAttribute('data-idx'), 10);
                const item = items.find(i => i.id === itemId);
                if (item) openModal(item);
            });
        });
    }

    /**
     * Buka Modal Detail Data Point
     */
    function openModal(item) {
        if (!modal) return;

        if (modalCategoryBadge) modalCategoryBadge.textContent = item.category;
        if (modalTitle) modalTitle.textContent = item.indicator;
        if (modalYear) modalYear.textContent = `${item.year} ${item.period ? `(${item.period})` : ''}`;
        if (modalValue) modalValue.textContent = `${formatNumberId(item.val)} ${item.unit}`;

        if (modalStatusDesc) {
            modalStatusDesc.textContent = item.isEstimate
                ? 'Angka Sementara / Proyeksi Model (belum merupakan realisasi final BPS).'
                : 'Angka Realisasi Resmi berdasarkan Berita Resmi Statistik (BRS) atau laporan instansi terkait.';
        }

        if (modalNotes) {
            modalNotes.textContent = item.notes || 'Tidak ada catatan anomali atau diskrepansi khusus pada titik data ini.';
        }

        if (modalSourcesList) {
            modalSourcesList.innerHTML = '';
            if (item.sources && item.sources.length > 0) {
                item.sources.forEach(s => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center justify-between p-2 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800';
                    div.innerHTML = `
                        <div>
                            <span class="font-bold text-slate-800 dark:text-slate-200 block text-[11px]">${s.publisher} — ${s.title}</span>
                            <span class="text-[10px] text-slate-400">${s.type || 'Publikasi'}</span>
                        </div>
                        ${s.url ? `
                            <a href="${s.url}" target="_blank" rel="noopener noreferrer" class="text-emerald-600 dark:text-emerald-400 font-semibold text-[11px] hover:underline flex items-center gap-1">
                                Tautan <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                        ` : ''}
                    `;
                    modalSourcesList.appendChild(div);
                });
            } else {
                modalSourcesList.innerHTML = `<span class="text-slate-400 italic">BPS Republik Indonesia</span>`;
            }
        }

        modal.classList.remove('opacity-0', 'pointer-events-none');
        modalCard.classList.remove('scale-95');
        modalCard.classList.add('scale-100');
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.add('opacity-0', 'pointer-events-none');
        modalCard.classList.remove('scale-100');
        modalCard.classList.add('scale-95');
    }

    if (btnCloseModal) btnCloseModal.addEventListener('click', closeModal);
    if (btnCloseModalFooter) btnCloseModalFooter.addEventListener('click', closeModal);
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    }

    // Filter Listeners
    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (filterCat) filterCat.addEventListener('change', applyFilter);
    if (filterYear) filterYear.addEventListener('change', applyFilter);
    if (filterStatus) filterStatus.addEventListener('change', applyFilter);

    // Pagination Listeners
    if (perPageSelect) {
        perPageSelect.addEventListener('change', () => {
            perPage = perPageSelect.value;
            currentPage = 1;
            renderTable();
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });
    }

    if (btnNext) {
        btnNext.addEventListener('click', () => {
            const effectivePerPage = perPage === 'all' ? filteredItems.length : parseInt(perPage, 10);
            const totalPages = effectivePerPage > 0 ? Math.ceil(filteredItems.length / effectivePerPage) : 1;
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });
    }

    // Export CSV
    const btnExportCsv = document.getElementById('btn-export-csv');
    if (btnExportCsv) {
        btnExportCsv.addEventListener('click', () => {
            const headers = ['No', 'Kategori', 'Indikator', 'Tahun', 'Periode', 'Nilai', 'Satuan', 'Status', 'Catatan', 'Sumber'];
            const rows = filteredItems.map((item, idx) => [
                idx + 1,
                `"${item.category}"`,
                `"${item.indicator.replace(/"/g, '""')}"`,
                item.year,
                `"${item.period}"`,
                item.val,
                `"${item.unit}"`,
                item.isEstimate ? 'Estimasi' : 'Realisasi',
                `"${(item.notes || '').replace(/"/g, '""')}"`,
                `"${item.sourceTitles.replace(/"/g, '""')}"`,
            ]);

            // Tambahkan UTF-8 BOM (\uFEFF) agar Microsoft Excel langsung membaca aksen dan pemisah dengan benar
            const csvContent = '\uFEFF' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `dataset_neraca_beras_indonesia_${Date.now()}.csv`;
            a.click();
            URL.revokeObjectURL(url);
            showToast('File CSV berhasil diunduh!');
        });
    }

    // Export JSON
    const btnExportJson = document.getElementById('btn-export-json');
    if (btnExportJson) {
        btnExportJson.addEventListener('click', () => {
            const exportData = {
                metadata: {
                    title: 'Basis Data Neraca Beras Indonesia 2024–2025',
                    exported_at: new Date().toISOString(),
                    total_records: filteredItems.length,
                },
                data: filteredItems.map(item => ({
                    id: item.id,
                    kategori: item.category,
                    indikator: item.indicator,
                    kode: item.indicatorCode,
                    tahun: item.year,
                    periode: item.period,
                    nilai: item.val,
                    satuan: item.unit,
                    is_estimasi: item.isEstimate,
                    catatan: item.notes,
                    sumber: item.sourceTitles,
                })),
            };

            const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `dataset_neraca_beras_indonesia_${Date.now()}.json`;
            a.click();
            URL.revokeObjectURL(url);
            showToast('File JSON berhasil diunduh!');
        });
    }

    // Copy Table to Clipboard
    const btnCopyTable = document.getElementById('btn-copy-table');
    if (btnCopyTable) {
        btnCopyTable.addEventListener('click', () => {
            const headers = ['No\tKategori\tIndikator\tTahun\tNilai\tSatuan\tStatus\tSumber'];
            const rows = filteredItems.map((item, idx) => 
                `${idx + 1}\t${item.category}\t${item.indicator}\t${item.year}\t${item.val}\t${item.unit}\t${item.isEstimate ? 'Estimasi' : 'Realisasi'}\t${item.sourceTitles}`
            );
            const text = [headers, ...rows].join('\n');
            navigator.clipboard.writeText(text).then(() => {
                showToast('Seluruh data tabel berhasil disalin ke clipboard!');
            }).catch(() => {
                showToast('Gagal menyalin data ke clipboard.');
            });
        });
    }

    // Initial render
    renderTable();
});
