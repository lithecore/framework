<?php

namespace Lithe\Support;

use InvalidArgumentException;
use RuntimeException;

/**
 * This class provides a simple caching mechanism using the filesystem.
 */
class Cache
{
    /**
     * The directory where cached data will be stored.
     *
     * @var string
     */
    private static $cacheDir = PROJECT_ROOT. 'storage/framework/cache';

    /**
     * A map of supported serializer names to their corresponding PHP functions.
     *
     * @var array
     */
    private static $serializerMap = [
        'serialize' => ['serialize', 'unserialize'],
        'json' => ['json_encode', 'json_decode'],
        'yaml' => ['yaml_emit', 'yaml_parse'], // Assuming YAML serialization is available
    ];

    /**
     * Ensures the cache directory exists.
     *
     * @throws RuntimeException If the directory cannot be created.
     */
    private static function ensureCacheDirExists()
    {
        if (!is_dir(self::$cacheDir)) {
            if (!mkdir(self::$cacheDir, 0777, true) && !is_dir(self::$cacheDir)) {
                throw new RuntimeException("Failed to create cache directory: " . self::$cacheDir);
            }
        }
    }

    /**
     * Generates the filename for a cached item based on its key.
     *
     * @param string $key The key of the cached item.
     *
     * @return string The filename for the cached item.
     */
    private static function getCacheFile($key)
    {
        return self::$cacheDir . DIRECTORY_SEPARATOR . md5($key) . '.cache';
    }

    /**
     * Stores data in the cache with an optional expiration time and serializer.
     *
     * @param string $key The key to identify the cached data.
     * @param mixed $data The data to be cached.
     * @param int $expiration (optional) The number of seconds the data should remain cached. Defaults to 3600 (1 hour).
     * @param string $serializer (optional) The serializer to use for storing the data. Defaults to 'serialize'.
     *
     * @throws RuntimeException If writing the data to the cache file fails.
     * @throws InvalidArgumentException If the provided serializer is not supported.
     */
    public static function add($key, $data, $expiration = 3600, $serializer = 'serialize')
    {
        self::ensureCacheDirExists();

        $cacheFile = self::getCacheFile($key);

        $cacheData = [
            'expiration' => time() + $expiration,
            'data' => self::serializeData($data, $serializer),
            'serializer' => $serializer,
        ];

        // Use file_put_contents with LOCK_EX for atomic writes (prevents partial writes)
        if (file_put_contents($cacheFile, json_encode($cacheData), LOCK_EX) === false) {
            throw new RuntimeException("Failed to write cache data to file: $cacheFile");
        }
    }

    /**
     * Serializes data using the specified serializer.
     *
     * @param mixed $data The data to be serialized.
     * @param string $serializer The name of the serializer to use.
     *
     * @throws InvalidArgumentException If the provided serializer is not supported.
     *
     * @return string The serialized data.
     */
    private static function serializeData($data, $serializer)
    {
        if (!isset(self::$serializerMap[$serializer])) {
            throw new InvalidArgumentException("Invalid serializer: $serializer");
        }

        $serializerFunction = self::$serializerMap[$serializer][0];
        return call_user_func($serializerFunction, $data);
    }

    /**
     * Deserializes data using the specified serializer.
     *
     * @param string $serializedData The serialized data.
     * @param string $serializer The name of the serializer used for serialization.
     *
     * @throws InvalidArgumentException If the provided serializer is not supported.
     *
     * @return mixed The deserialized data.
     */
    private static function unserializeData($serializedData, $serializer)
    {
        if (!isset(self::$serializerMap[$serializer])) {
            throw new InvalidArgumentException("Invalid serializer: $serializer");
        }

        $unserializerFunction = self::$serializerMap[$serializer][1];

        // Para 'json', o segundo parâmetro deve ser um array.
        if ($serializer === 'json') {
            return call_user_func($unserializerFunction, $serializedData, true); // true para associativo
        }

        // Para 'serialize' e 'yaml', não há parâmetros adicionais.
        return call_user_func($unserializerFunction, $serializedData);
    }

    /**
     * Retrieves data from the cache for a given key.
     *
     * @param string $key The key to identify the cached data.
     *
     * @return mixed The cached data, or null if not found or expired.
     * @throws InvalidArgumentException If the provided serializer is not supported.
     */
    public static function get($key)
    {
        // 1. Get the filename for the cached item based on the key
        $cacheFile = self::getCacheFile($key);

        // 2. Check if the cache file exists
        if (!file_exists($cacheFile)) {
            // If not found, return null
            return null;
        }

        // 3. Read the contents of the cache file
        $cacheData = json_decode(file_get_contents($cacheFile), true);

        // 4. Check if the cache has expired (current time vs. expiration time)
        if (time() > $cacheData['expiration']) {
            // If expired, delete the cache file and return null
            unlink($cacheFile);
            return null;
        }

        // 5. Deserialize the data using the specified serializer
        return self::unserializeData($cacheData['data'], $cacheData['serializer']);
    }

    /**
     * Invalidates a cached item by removing its corresponding file.
     *
     * @param string $key The key used to identify the cached data.
     */
    public static function invalidate($key)
    {
        // 1. Get the filename for the cached item based on the key
        $cacheFile = self::getCacheFile($key);

        // 2. Check if the cache file exists
        if (file_exists($cacheFile)) {
            // 3. If it exists, delete the file
            unlink($cacheFile);
        }
    }

    /**
     * Retrieves data from the cache or executes a callback to fetch and cache the data if not found.
     *
     * @param string $key The key used to identify the cached data.
     * @param callable $callback The callback function to fetch the data if it is not found in the cache.
     * @param int $expiration (optional) The number of seconds the data should remain cached. Defaults to 3600 (1 hour).
     * @param string $serializer (optional) The serializer to use for storing the data. Defaults to 'json'.
     *
     * @return mixed The cached data, or the data fetched by the callback if not found in the cache.
     */
    public static function remember($key, $callback, $expiration = 3600, $serializer = 'serialize')
    {
        // Attempt to retrieve the cached data using the provided key.
        $cachedData = self::get($key);

        // If the cached data is not found or has expired:
        if (!$cachedData) {
            // Execute the callback to fetch fresh data.
            $cachedData = $callback();

            // Store the fresh data in the cache.
            self::add($key, $cachedData, $expiration, $serializer);
        }

        // Return the cached (or newly fetched) data.
        return $cachedData;
    }
}
