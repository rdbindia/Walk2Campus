<?php

namespace Tests\Feature;

use App\Services\CacheInterface;
use App\Services\CacheSession;
use App\Services\WeatherAppService;
use Exception;
use Mockery;
use Tests\TestCase;

class WeatherAppControllerTest extends TestCase
{
    private CacheSession $cache;
    protected CacheInterface $cacheInterface;
    protected WeatherAppService $weatherAppService;
    protected array $weatherData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = new CacheSession();
        $this->cacheInterface = Mockery::mock(CacheInterface::class);
        $this->weatherAppService = Mockery::mock(WeatherAppService::class);
        $this->weatherData = [
            "last_updated" => "2022-04-04 16:30",
            "temperature" => 70.0,
            "is_day" => true,
            "wind_mph" => 13.6,
            "wind_degree" => 210,
            "wind_direction" => "SSW",
            "pressure" => 30.0,
            "precipitation" => 0.0,
            "humidity" => 30,
            "cloud" => 0,
            "feels_like" => 70.0,
            "visibility" => 9.0,
            "uv" => 6.0,
            "gust_mph" => 7.2
        ];
    }

    public function testWhenSuccessThenCachedDataIsReturned(): void
    {
        $this->cache->set('weather_90210', $this->weatherData, 300); // Cached for 5 minutes

        $response = $this->get('/weather/90210');

        $response->assertStatus(200)
            ->assertJson(['temperature' => 70.0]);
    }

    public function testit_handles_api_errors_gracefully()
    {
        $this->cacheInterface = Mockery::mock(CacheInterface::class);

        $this->weatherAppService
            ->shouldReceive('fetchWeatherData')
            ->with('12345')
            ->andThrow(new Exception('Error fetching weather data'));

        $response = $this->get('/weather/12345');

        // Assert that the error is handled correctly
        $response->assertStatus(500)
            ->assertJsonFragment(['error' => 'Unable to retrieve weather data']);
    }
}
