<?php

namespace Lithe\Support;

use Dotenv\Dotenv;
use RuntimeException;

if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}

class Env
{
    /**
     * Loads environment variables from the .env file.
     *
     * @param string $path Path to the .env file.
     * @throws RuntimeException If the .env file cannot be loaded.
     */
    public static function load(string $path = PROJECT_ROOT)
    {

        if (!file_exists($path . '.env')) {
            throw new RuntimeException("The .env file was not found at: $path");
        }

        try {
            $dotenv = Dotenv::createImmutable($path);
            $dotenv->safeLoad(); // Use safeLoad to avoid throwing exceptions for missing .env
        } catch (\Dotenv\Exception\InvalidPathException $e) {
            // Error handling if the .env file cannot be loaded
            Log::error($e);
            die("Error loading .env file: " . $e->getMessage());
        }
    }

    /**
     * Gets the value of an environment variable.
     *
     * @param string $key Name of the environment variable.
     * @param mixed $default Default value to return if the variable is not set.
     * @return mixed Value of the environment variable or the default value.
     */
    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }

    /**
     * Sets an environment variable.
     *
     * @param string $key Name of the environment variable.
     * @param mixed $value Value of the environment variable.
     */
    public static function set(string $key, $value)
    {
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }

    /**
     * Checks if an environment variable is defined.
     *
     * @param string $key Name of the environment variable.
     * @return bool True if the variable is defined, false otherwise.
     */
    public static function has(string $key): bool
    {
        return isset($_ENV[$key]);
    }
}
