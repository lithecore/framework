<?php

namespace Lithe\Support\Tests;

use PHPUnit\Framework\TestCase;
use Lithe\Support\Session;
use RuntimeException;

class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        // Initializes the session for testing
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function tearDown(): void
    {
        // Clears the session after each test
        $_SESSION = [];
        session_write_close();
    }

    /**
     * Tests the put and get methods of the Session class.
     */
    public function testPutAndGet()
    {
        Session::put('key', 'value');
        $this->assertEquals('value', Session::get('key'));
    }

    /**
     * Tests the get method with a default value.
     */
    public function testGetWithDefault()
    {
        $this->assertEquals('default', Session::get('nonexistent_key', 'default'));
    }

    /**
     * Tests the forget method to remove a specific session key.
     */
    public function testForget()
    {
        Session::put('key', 'value');
        Session::forget('key');
        $this->assertNull(Session::get('key'));
    }

    /**
     * Tests the forget method to remove multiple session keys.
     */
    public function testForgetMultiple()
    {
        Session::put('key1', 'value1');
        Session::put('key2', 'value2');
        Session::forget(['key1', 'key2']);
        $this->assertNull(Session::get('key1'));
        $this->assertNull(Session::get('key2'));
    }

    /**
     * Tests if the session is active.
     */
    public function testIsActive()
    {
        $this->assertTrue(Session::isActive());
    }

    /**
     * Tests the regenerate method to change the session ID.
     */
    public function testRegenerateId()
    {
        // Initializes the session if not already active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Save the current session ID
        $oldSessionId = Session::getId();

        // Regenerate the session ID
        Session::regenerate();

        // Get the new session ID
        $newSessionId = Session::getId();

        // Check if the session ID has changed after regeneration
        $this->assertNotEquals($oldSessionId, $newSessionId);
    }

    /**
     * Tests the getId method to ensure it returns a string.
     */
    public function testGetId()
    {
        $this->assertIsString(Session::getId());
    }

    /**
     * Tests the all method to retrieve all session data.
     */
    public function testAll()
    {
        Session::put('key1', 'value1');
        Session::put('key2', 'value2');
        $all = Session::all();
        $this->assertArrayHasKey('key1', $all);
        $this->assertArrayHasKey('key2', $all);
        $this->assertEquals('value1', $all['key1']);
        $this->assertEquals('value2', $all['key2']);
    }

    /**
     * Tests the has method to check if a session key exists.
     */
    public function testHas()
    {
        Session::put('key', 'value');
        $this->assertTrue(Session::has('key'));
        $this->assertFalse(Session::has('nonexistent_key'));
    }

    /**
     * Tests the magic methods of the Session class.
     */
    public function testMagicMethods()
    {
        $session = new Session();
        $session->testKey = 'testValue';
        $this->assertEquals('testValue', $session->testKey);
        $this->assertEquals('testValue', Session::get('testKey'));
    }

    /**
     * Tests that a RuntimeException is thrown if the session is inactive.
     */
    public function testCheckSessionActiveException()
    {
        session_write_close(); // Close the session to simulate an inactive session
        $this->expectException(RuntimeException::class);
        Session::put('key', 'value');
    }
}
