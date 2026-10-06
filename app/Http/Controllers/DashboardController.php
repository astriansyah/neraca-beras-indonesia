<?php

namespace App\Http\Controllers;

use App\Repositories\DatasetRepository;
use App\Services\RiceStatisticsService;
use Illuminate\Contracts\View\View;

/**
 * Halaman publik dashboard (read-only). Angka di halaman berasal dari
 * RiceStatisticsService (server-side) dan API JSON (client-side chart).
 */
class DashboardController extends Controller
{
    public function __construct(
        protected RiceStatisticsService $statsService,
        protected DatasetRepository $repo
    ) {}

    public function index(): View
    {
        return $this->page('dashboard', [
            'stats' => $this->statsService->getSummary(),
        ]);
    }

    public function production(): View
    {
        return $this->page('production', [
            'stats' => $this->statsService->getProduction(),
        ]);
    }

    public function consumption(): View
    {
        return $this->page('consumption', [
            'stats' => $this->statsService->getConsumption(),
        ]);
    }

    public function trade(): View
    {
        return $this->page('trade', [
            'stats' => $this->statsService->getTrade(),
        ]);
    }

    public function stock(): View
    {
        return $this->page('stock', [
            'stats' => $this->statsService->getStock(),
        ]);
    }

    public function balance(): View
    {
        return $this->page('balance', [
            'stats' => $this->statsService->getBalance(),
        ]);
    }

    public function simulation(): View
    {
        return $this->page('simulation', [
            'stats' => $this->statsService->getSimulationParameters(),
        ]);
    }

    public function data(): View
    {
        return $this->page('data', [
            'dataPoints' => $this->repo->getAllDataPointsWithRelations(),
            'sources' => $this->repo->getAllSources(),
        ]);
    }

    public function methodology(): View
    {
        return $this->page('methodology', [
            'notes' => $this->repo->getAllNotes(),
        ]);
    }

    /** @param array<string,mixed> $data */
    private function page(string $key, array $data = []): View
    {
        return view("pages.{$key}", $data + [
            'pageKey' => $key,
            'page' => config("dashboard.pages.{$key}"),
        ]);
    }
}
