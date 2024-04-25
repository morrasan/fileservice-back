<?php

namespace App\Console\Commands;

use App\Services\OpenMeteoDataService;
use Illuminate\Console\Command;

class GetOpenMeteoDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-open-meteo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(OpenMeteoDataService $openMeteoDataService)
    {
        echo $openMeteoDataService->getTemperatureFromKatowice();
    }
}
