<?php

use PHPUnit\Framework\TestCase;
use Lithe\Support\Cache;

class CacheTest extends TestCase
{
    private static $cacheDir = '../storage/framework/cache';

    protected function setUp(): void
    {
        // Clears the cache directory before each test
        if (is_dir(self::$cacheDir)) {
            $files = glob(self::$cacheDir . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
        } else {
            mkdir(self::$cacheDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        // Clears the cache directory after each test
        $files = glob(self::$cacheDir . '/*');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    /**
     * Tests adding and retrieving data from the cache.
     */
    public function testAddAndGetCache()
    {
        $key = 'test_key';
        $data = ['foo' => 'bar'];
        Cache::add($key, $data, 3600, 'json'); // Adds data to the cache with a 1-hour expiration

        $cachedData = Cache::get($key); // Retrieves data from the cache
        $this->assertEquals($data, $cachedData); // Asserts that the retrieved data matches the added data
    }

    /**
     * Tests cache expiration functionality.
     */
    public function testCacheExpiration()
    {
        $key = 'test_key_expiration';
        $data = ['foo' => 'bar'];
        Cache::add($key, $data, 1, 'json'); // Adds data to the cache with a 1-second expiration

        sleep(2); // Waits 2 seconds to ensure that the cache expires

        $cachedData = Cache::get($key); // Retrieves data from the cache
        $this->assertNull($cachedData); // Asserts that the cache is null (expired)
    }

    /**
     * Tests cache invalidation functionality.
     */
    public function testInvalidateCache()
    {
        $key = 'test_key_invalidate';
        $data = ['foo' => 'bar'];
        Cache::add($key, $data, 3600, 'json'); // Adds data to the cache with a 1-hour expiration

        Cache::invalidate($key); // Invalidates the cache

        $cachedData = Cache::get($key); // Retrieves data from the cache
        $this->assertNull($cachedData); // Asserts that the cache is null (invalidated)
    }

    /**
     * Tests handling of invalid serializer.
     */
    public function testInvalidSerializer()
    {
        $this->expectException(InvalidArgumentException::class); // Expects an InvalidArgumentException

        $key = 'test_key_invalid_serializer';
        $data = ['foo' => 'bar'];
        Cache::add($key, $data, 3600, 'invalid_serializer'); // Adds data to the cache with an invalid serializer

        Cache::get($key); // Attempts to retrieve data from the cache
    }
    
    /**
     * Tests the remember method functionality.
     */
    public function testRememberMethod()
    {
        $key = 'test_key_remember';
        $data = ['foo' => 'bar'];

        $result = Cache::remember($key, function () use ($data) {
            return $data; // Returns the data to cache
        }, 3600, 'json'); // Caches the data with a 1-hour expiration

        $this->assertEquals($data, $result); // Asserts that the data returned by remember matches the cached data

        // Tests retrieval of existing cache
        $resultFromCache = Cache::get($key); // Retrieves data from the cache
        $this->assertEquals($data, $resultFromCache); // Asserts that the retrieved data matches the cached data
    }
}
