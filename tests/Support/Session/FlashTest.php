<?php

namespace Tests\Support\Session;

use PHPUnit\Framework\TestCase;
use Lithe\Support\Session\Flash;

class FlashTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Initializes the session for testing
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function tearDown(): void
    {
        // Clears the session after each test
        $this->clearSession();  // Clears session data
        parent::tearDown();
    }

    protected function clearSession()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];  // Clears session data
        }
    }

    /**
     * Tests setting and getting a flash message.
     */
    public function testSetAndGetFlashMessage()
    {
        Flash::set('testMessage', 'This is a test message.'); // Sets a flash message

        $this->assertEquals('This is a test message.', Flash::get('testMessage')); // Asserts that the flash message is correctly retrieved
        $this->assertNull(Flash::get('testMessage'));  // Asserts that the flash message is null after reading
    }

    /**
     * Tests the magic set method for flash messages.
     */
    public function testMagicSet()
    {
        $flash = new Flash();
        $flash->testMagic = 'Testing magic set'; // Sets a flash message using magic method

        $this->assertEquals('Testing magic set', Flash::get('testMagic')); // Asserts that the flash message is correctly retrieved
        $this->assertNull(Flash::get('testMagic'));  // Asserts that the flash message is null after reading
    }

    /**
     * Tests the magic get method for flash messages.
     */
    public function testMagicGet()
    {
        Flash::set('testMagicGet', 'Testing magic get'); // Sets a flash message

        $flash = new Flash();
        $this->assertEquals('Testing magic get', $flash->testMagicGet); // Asserts that the flash message is correctly retrieved using magic method
        $this->assertNull($flash->testMagicGet);  // Asserts that the flash message is null after reading
    }

    /**
     * Tests checking if a flash message exists.
     */
    public function testHasFlashMessage()
    {
        Flash::set('flashExists', 'Message exists'); // Sets a flash message

        $this->assertTrue(Flash::has('flashExists')); // Asserts that the flash message exists
        $this->assertFalse(Flash::has('nonExistingFlash')); // Asserts that a non-existing flash message does not exist
    }

    /**
     * Tests keeping a flash message across requests.
     */
    public function testKeepFlashMessage()
    {
        Flash::set('flashToKeep', 'Message to keep'); // Sets a flash message
        Flash::keep('flashToKeep'); // Marks the message to be kept

        // Simulates session restart
        session_write_close(); // Closes the current session
        session_start();       // Starts a new session to simulate a new request

        // Checks if the flash message is still available
        $this->assertEquals('Message to keep', Flash::get('flashToKeep')); // Asserts that the flash message is retained
    }
}
