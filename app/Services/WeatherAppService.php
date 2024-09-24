<?php

namespace App\Services;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class WeatherAppService
{
    private string $apiKey;
    private string $apiUrl;
    //private readonly CacheInterface $cache;

    public function __construct()
    {
//        $this->cache = $cache;
        $this->apiKey = config('services.fakeweather.api_key');
        $this->apiUrl = config('services.fakeweather.api_url');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function getWeather(string $zipCode)
    {
        $cacheKey = 'weather_' . $zipCode;
        $cachedWeather = resolve(CacheInterface::class)->get($cacheKey);

        if ($cachedWeather) {
            return $cachedWeather; // Return cached result
        }

        // Fetch weather data from the API
        $responseData = $this->fetchWeatherData($zipCode);

        // Cache the result for 5 minutes
        resolve(CacheInterface::class)->set($cacheKey, $responseData, 300);

        return $responseData;
    }

    /**
     * @throws Exception
     */
    public function fetchWeatherData(string $zipCode)
    {
        $url = $this->apiUrl . "?" . $zipCode;
        $headers = [
            'Authorization: Basic ' . base64_encode($this->apiKey)
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode!=200) {
            throw new Exception("Status Code {$httpCode}:  Error fetching weather data");
        }

        $jsonDecode = json_decode($response, true);
        return $jsonDecode['data'];
    }
}
