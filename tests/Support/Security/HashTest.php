<?php

namespace Tests\Support\Security;

use PHPUnit\Framework\TestCase;
use Lithe\Support\Security\Hash;
use InvalidArgumentException;

class HashTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set up any necessary initial state
    }

    /**
     * Tests successful hashing of a value.
     */
    public function testMakeSuccess()
    {
        $value = 'password123';
        $hash = Hash::make($value, ['cost' => 12]); // Hashes the value with a specified cost

        $this->assertNotEmpty($hash); // Asserts that the hash is not empty
        $this->assertTrue(Hash::check($value, $hash)); // Asserts that the hash matches the original value
    }

    /**
     * Tests that an exception is thrown with an invalid cost value.
     */
    public function testMakeThrowsExceptionWithInvalidCost()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The cost must be between 4 and 31.'); // Expected exception message

        Hash::make('password123', ['cost' => 32]); // Invalid cost value
    }

    /**
     * Tests successful verification of a hashed value.
     */
    public function testCheckSuccess()
    {
        $value = 'password123';
        $hash = Hash::make($value, ['cost' => 10]); // Hashes the value

        $this->assertTrue(Hash::check($value, $hash)); // Asserts that the hash matches the original value
    }

    /**
     * Tests failure of hash verification with an incorrect value.
     */
    public function testCheckFailure()
    {
        $value = 'password123';
        $wrongValue = 'wrongpassword'; // Incorrect value
        $hash = Hash::make($value, ['cost' => 10]); // Hashes the correct value

        $this->assertFalse(Hash::check($wrongValue, $hash)); // Asserts that the incorrect value does not match the hash
    }

    /**
     * Tests if a hash needs rehashing due to a change in cost.
     */
    public function testNeedsRehashSuccess()
    {
        $value = 'password123';
        $hash = Hash::make($value, ['cost' => 10]); // Hashes the value

        $this->assertFalse(Hash::needsRehash($hash, ['cost' => 10])); // Asserts no rehashing is needed with the same cost
        $this->assertTrue(Hash::needsRehash($hash, ['cost' => 8])); // Asserts that rehashing is needed with a lower cost
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Clean up any state or mocks
    }
}
