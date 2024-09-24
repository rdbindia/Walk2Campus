<?php

namespace App\Services;

class CacheSession implements CacheInterface
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); // Start session
        }

        if (!isset($_SESSION['cache'])) {
            $_SESSION['cache'] = []; // Initialize cache array
        }
    }

    /**
     * @param string $key
     * @param float|int|array|string|null $value
     * @param int $duration Duration in seconds
     */
    public function set(string $key, float|array|int|string|null $value, int $duration): void
    {
        $_SESSION['cache'][$key] = [
            'expires_at' => time() + $duration,
            'value' => $value
        ];
    }

    /**
     * @param string $key
     * @return int|string|float|array|null
     */
    public function get(string $key): float|int|array|string|null
    {
        if (isset($_SESSION['cache'][$key])) {
            $cache = $_SESSION['cache'][$key];

            if ($cache['expires_at'] > time()) {
                return $cache['value'];
            }

            //Unsetting Cache after 5 mins
            unset($_SESSION['cache'][$key]);
        }

        return null;
    }
}
