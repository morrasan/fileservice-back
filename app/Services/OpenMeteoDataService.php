<?php

namespace App\Services;

use App\Repositories\FileRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class OpenMeteoDataService {

    private string $url = 'https://api.open-meteo.com/v1/forecast?latitude=50.25841&longitude=19.02754&hourly=temperature_2m&timezone=Europe%2FWarsaw&forecast_days=1';

    public function __construct(private readonly FileRepository $fileRepository) {}

    /**
     * Get temperature from API open-meteo.com for Katowice on current hour
     *
     * @return int
     * @throws \Exception
     */
    public function getTemperatureFromKatowice (): int {

        // get data with off ssl verification
        $response = Http::withoutVerifying()->get($this->url);

        if ($response->successful()) {

            $data = $response->json();

            $dateTime = Carbon::now();

            // format data for the open-meteo
            $normalDateTime = $dateTime->format('Y-m-d\TH:00');

            $temperatureIndex = array_search($normalDateTime, $data['hourly']['time']);

            return $data['hourly']['temperature_2m'][$temperatureIndex];

        } else {

            throw new \Exception('Error: ' . $response->body());
        }
    }

    /**
     * Store the temperature to database by file ID
     *
     * @param int $fileId
     * @param int $temperature
     *
     * @return void
     */
    public function storeTemperatureToFile (int $fileId, int $temperature): void {

        $file = $this->fileRepository->getById($fileId);

        $file->temperature = $temperature;

        $file->save();
    }
}
