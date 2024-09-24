<?php

namespace App\Services;

interface CacheInterface
{
    /**
     * @param string $key
     * @param float|int|array|string|null $value
     * @param int $duration Duration in seconds
     */
    public function set(string $key, float|int|array|string|null $value, int $duration): void;

    /**
     * @param string $key
     * @return int|string|float|array|null
     */
    public function get(string $key): float|int|array|string|null;
}
