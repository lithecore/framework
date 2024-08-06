<?php

namespace Lithe\Support\Security;

use Illuminate\Encryption\Encrypter;
use Lithe\Contracts\Encryption\CryptException;
use Lithe\Support\Env;

class Crypt
{
    protected static $encrypter;

    /**
     * Returns an instance of Encrypter.
     *
     * @return Encrypter
     * @throws CryptException If the encryption key is invalid or not set.
     */
    protected static function encrypter(): Encrypter
    {
        if (!static::$encrypter) {
            $key = Env::get('APP_KEY');
            if (!$key) {
                throw new CryptException('APP_KEY environment variable not set.');
            }

            // Decode the base64 encoded key
            $decodedKey = base64_decode($key, true);
            if ($decodedKey === false || strlen($decodedKey) !== 32) {
                throw new CryptException('Invalid APP_KEY. Ensure it is a valid base64 encoded key with a length of 32 bytes.');
            }

            static::$encrypter = new Encrypter($decodedKey, 'AES-256-CBC');
        }

        return static::$encrypter;
    }

    /**
     * Encrypts the provided data.
     *
     * @param string $data Data to be encrypted.
     * @return string Encrypted data in base64 format.
     * @throws CryptException If an error occurs while encrypting the data.
     */
    public static function encrypt(string $data): string
    {
        try {
            return static::encrypter()->encrypt($data);
        } catch (\Exception $e) {
            throw new CryptException('Error encrypting data: ' . $e->getMessage());
        }
    }

    /**
     * Decrypts the provided data.
     *
     * @param string $encryptedData Encrypted data in base64 format.
     * @return string Decrypted data.
     * @throws CryptException If an error occurs while decrypting the data.
     */
    public static function decrypt(string $encryptedData): string
    {
        try {
            return static::encrypter()->decrypt($encryptedData);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            throw new CryptException('Error decrypting data: ' . $e->getMessage());
        }
    }
}
