<?php

namespace App\Http\Controllers;

use App\Services\WeatherAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class WeatherAppController extends Controller
{

    public function __construct(
        private readonly WeatherAppService $weatherService
    )
    {
    }

    public function displayWeatherData($postal_code): JsonResponse
    {
        try {
            $weatherData = $this->weatherService->getWeather($postal_code);

            return response()->json($weatherData);

        } catch (\Exception|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            Log::error("displayWeatherData: {$e->getMessage()}",
                [
                    'trace' => $e->getTraceAsString(),
                ]
            );

            return response()->json(['error' => 'Unable to retrieve weather data'], 500);
        }
    }
}
