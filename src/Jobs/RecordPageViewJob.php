<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Prajwal89\Analytics\Services\AnalyticService;

class RecordPageViewJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $url,
        public array $analyticData,
        public ?int $userId,
    ) {}

    public function handle(): void
    {
        AnalyticService::recordPageView(
            url: $this->url,
            analyticData: $this->analyticData,
            userId: $this->userId,
        );
    }
}
