<?php

namespace App\Observers;

use App\Models\DataPoint;
use App\Services\RiceStatisticsService;

class DataPointObserver
{
    public function __construct(
        protected RiceStatisticsService $service
    ) {}

    public function saved(DataPoint $dataPoint): void
    {
        $this->service->clearCache();
    }

    public function deleted(DataPoint $dataPoint): void
    {
        $this->service->clearCache();
    }
}
