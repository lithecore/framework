<?php

namespace Lithe\Support\Security;

use Illuminate\Hashing\BcryptHasher;

class Hash
{
    /**
     * Instância do BcryptHasher do Laravel.
     *
     * @var BcryptHasher|null
     */
    protected static $hasher = null;

    /**
     * Inicializa o BcryptHasher se não estiver inicializado.
     */
    protected static function BcryptHasher()
    {
        if (!static::$hasher) {
            static::$hasher = new BcryptHasher();
        }
    }

    /**
     * Cria um hash a partir de uma string.
     *
     * @param string $value
     * @param array $options
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function make(string $value, array $options = []): string
    {
        static::BcryptHasher();

        $cost = $options['cost'] ?? 10;

        if ($cost < 4 || $cost > 31) {
            throw new \InvalidArgumentException('The cost must be between 4 and 31.');
        }

        return static::$hasher->make($value, ['rounds' => $cost]);
    }

    /**
     * Verifica se a string corresponde ao hash.
     *
     * @param string $value
     * @param string $hash
     * @return bool
     */
    public static function check(string $value, string $hash): bool
    {
        static::BcryptHasher();

        return static::$hasher->check($value, $hash);
    }

    /**
     * Rehashes o valor fornecido se necessário.
     *
     * @param string $hash
     * @param array $options
     * @return bool
     */
    public static function needsRehash(string $hash, array $options = []): bool
    {
        static::BcryptHasher();

        $cost = $options['cost'] ?? 10;
        return static::$hasher->needsRehash($hash, ['rounds' => $cost]);
    }
}
