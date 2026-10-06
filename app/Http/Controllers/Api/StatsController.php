<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataPoint;
use App\Models\Source;
use App\Services\RiceStatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller API publik read-only untuk data statistik neraca beras.
 * Seluruh endpoint bertipe GET dan bebas autentikasi.
 */
class StatsController extends Controller
{
    public function __construct(
        protected RiceStatisticsService $service
    ) {}

    public function summary(): JsonResponse
    {
        $data = $this->service->getSummary();

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'warnings' => $data['warnings'] ?? [],
            'meta' => $this->meta(),
        ]);
    }

    public function production(): JsonResponse
    {
        $data = $this->service->getProduction();

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'warnings' => $data['warnings'] ?? [],
            'meta' => $this->meta(),
        ]);
    }

    public function consumption(): JsonResponse
    {
        $data = $this->service->getConsumption();

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'warnings' => $data['warnings'] ?? [],
            'meta' => $this->meta(),
        ]);
    }

    public function trade(): JsonResponse
    {
        $data = $this->service->getTrade();

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'warnings' => $data['warnings'] ?? [],
            'meta' => $this->meta(),
        ]);
    }

    public function stock(): JsonResponse
    {
        $data = $this->service->getStock();

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'warnings' => $data['warnings'] ?? [],
            'meta' => $this->meta(),
        ]);
    }

    public function balance(): JsonResponse
    {
        $data = $this->service->getBalance();

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'warnings' => $data['warnings'] ?? [],
            'meta' => $this->meta(),
        ]);
    }

    public function simulation(): JsonResponse
    {
        $data = $this->service->getSimulationParameters();

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'warnings' => $data['warnings'] ?? [],
            'meta' => $this->meta(),
        ]);
    }

    /**
     * Endpoint semua data points untuk tabel, filter, dan ekspor.
     */
    public function dataPoints(Request $request): JsonResponse
    {
        $query = DataPoint::with(['indicator.category', 'sources']);

        if ($year = $request->query('tahun')) {
            $query->where('tahun', (int) $year);
        }

        if ($cat = $request->query('kategori')) {
            $query->whereHas('indicator.category', fn ($q) => $q->where('slug', $cat));
        }

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('catatan', 'like', "%{$search}%")
                  ->orWhereHas('indicator', fn ($qi) => $qi->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
            });
        }

        $points = $query->orderBy('indicator_id')->orderBy('tahun')->orderBy('sort')->get();

        return response()->json([
            'status' => 'success',
            'total' => $points->count(),
            'data' => $points,
            'meta' => $this->meta(),
        ]);
    }

    /**
     * Endpoint seluruh sumber data resmi.
     */
    public function sources(): JsonResponse
    {
        $sources = Source::orderBy('publisher')->orderBy('title')->get();

        return response()->json([
            'status' => 'success',
            'total' => $sources->count(),
            'data' => $sources,
            'meta' => $this->meta(),
        ]);
    }

    /**
     * Metadata standar untuk setiap respons API.
     */
    protected function meta(): array
    {
        return [
            'app' => 'Neraca Beras Indonesia 2024–2025',
            'years' => config('dashboard.years'),
            'timestamp' => now()->toIso8601String(),
            'read_only' => true,
        ];
    }
}
