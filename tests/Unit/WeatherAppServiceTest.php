<?php

namespace Tests\Unit;

use App\Services\CacheInterface;
use App\Services\WeatherAppService;
use Mockery;
use Tests\TestCase;
use Exception;

class WeatherAppServiceTest extends TestCase
{
    protected WeatherAppService $weatherAppService;
    protected CacheInterface $cacheInterface;

    protected function setUp(): void
    {
        parent::setUp();

        $this->weatherAppService = Mockery::mock(WeatherAppService::class);
        $this->cacheInterface = Mockery::mock(CacheInterface::class);
    }

    public function testWhenThereIsAnExceptionThenAnErrorMessageIsReturned(): void
    {
        $this->weatherAppService = Mockery::mock(WeatherAppService::class, [$this->cacheInterface])
            ->makePartial();

        $this->weatherAppService
            ->shouldReceive('fetchWeatherData')
            ->with('12345')
            ->once()
            ->andThrow(new Exception('Error fetching weather data'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error fetching weather data');

        $this->weatherAppService->getWeather('12345');
    }

    public function testWhenWeGetASuccessResponseThenItReturnsCachedWeatherData(): void
    {
        $cachedWeatherData = [
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

        $this->cacheInterface
            ->shouldReceive('get')
            ->with('weather_12345')
            ->andReturn($cachedWeatherData);


        $this->weatherAppService = Mockery::mock(WeatherAppService::class, [$this->cacheInterface])
            ->makePartial();

        $this->weatherAppService
            ->shouldReceive('fetchWeatherData')
            ->with('12345')
            ->andReturn($cachedWeatherData);

        $this->cacheInterface
            ->shouldReceive('set')
            ->with('weather_12345', $cachedWeatherData, 300);

        $result = $this->weatherAppService->getWeather('12345');

        $this->assertEquals($cachedWeatherData, $result);
    }
}
