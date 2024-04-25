<?php

namespace App\Jobs;

use App\Services\OpenMeteoDataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreOpenMeteoDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $fileId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $fileId) {

        $this->fileId = $fileId;
    }

    /**
     * Execute the job.
     */
    public function handle(OpenMeteoDataService $openMeteoDataService): void {

        $temperature = $openMeteoDataService->getTemperatureFromKatowice();

        $openMeteoDataService->storeTemperatureToFile($this->fileId, $temperature);
    }
}
