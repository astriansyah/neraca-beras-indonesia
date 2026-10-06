<?php

namespace App\Repositories;

use App\Models\Breakdown;
use App\Models\Category;
use App\Models\DataPoint;
use App\Models\Indicator;
use App\Models\Note;
use App\Models\Source;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Repository data neraca beras — lapisan akses data dari SQLite dengan caching.
 */
class DatasetRepository
{
    /**
     * Ambil data point berdasarkan kode indikator dan tahun.
     */
    public function getDataPoint(string $indicatorCode, int $year): ?DataPoint
    {
        return DataPoint::whereHas('indicator', fn ($q) => $q->where('code', $indicatorCode))
            ->where('tahun', $year)
            ->first();
    }

    /**
     * Ambil seluruh data points untuk satu kode indikator.
     *
     * @return Collection<int, DataPoint>
     */
    public function getIndicatorPoints(string $indicatorCode): Collection
    {
        return DataPoint::whereHas('indicator', fn ($q) => $q->where('code', $indicatorCode))
            ->orderBy('tahun')
            ->orderBy('sort')
            ->get();
    }

    /**
     * Ambil rincian (breakdown) negara untuk indikator dan tahun tertentu.
     *
     * @return Collection<int, Breakdown>
     */
    public function getBreakdowns(string $indicatorCode, int $year): Collection
    {
        return Breakdown::whereHas('indicator', fn ($q) => $q->where('code', $indicatorCode))
            ->where('tahun', $year)
            ->orderBy('sort')
            ->get();
    }

    /**
     * Ambil catatan kualitas data berdasarkan scope halaman.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getNotesForScope(string $scope): array
    {
        return Note::where('type', 'quality')
            ->whereJsonContains('scopes', $scope)
            ->orderBy('sort')
            ->get(['code', 'title', 'body', 'severity', 'badge', 'scopes'])
            ->toArray();
    }

    /**
     * Ambil seluruh catatan dataset dan kualitas data.
     *
     * @return Collection<int, Note>
     */
    public function getAllNotes(): Collection
    {
        return Note::with('category')->orderBy('sort')->get();
    }

    /**
     * Ambil semua data points beserta relasi lengkap (untuk tabel data).
     *
     * @return Collection<int, DataPoint>
     */
    public function getAllDataPointsWithRelations(): Collection
    {
        return DataPoint::with(['indicator.category', 'sources'])
            ->orderBy('indicator_id')
            ->orderBy('tahun')
            ->orderBy('sort')
            ->get();
    }

    /**
     * Ambil seluruh sumber data resmi.
     *
     * @return Collection<int, Source>
     */
    public function getAllSources(): Collection
    {
        return Source::orderBy('publisher')->orderBy('title')->get();
    }
}
