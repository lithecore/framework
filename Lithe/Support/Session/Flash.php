<?php

namespace Lithe\Support\Session;

use Lithe\Support\Session;

/**
 * Flash support for handling flash messages in sessions.
 */
class Flash
{
    /**
     * Magic method to set properties in the session as flash messages.
     *
     * @param string $name  The name of the flash message.
     * @param mixed $value  The value of the flash message.
     */
    public function __set(string $name, mixed $value)
    {
        self::set($name, $value);
    }

    /**
     * Method to set properties in the session as flash messages.
     *
     * @param string $name  The name of the flash message.
     * @param mixed $value  The value of the flash message.
     */
    public static function set(string $name, mixed $value)
    {
        $name = "__$name";
        Session::put($name, $value);
    }

    /**
     * Magic method to get and remove properties from the session as flash messages.
     *
     * @param string $name  The name of the flash message.
     * @return mixed|null   The value of the flash message.
     */
    public function __get(string $name): mixed
    {
        return self::get($name);
    }

    /**
     * Method to get and remove properties from the session as flash messages.
     *
     * @param string $name  The name of the flash message.
     * @return mixed|null   The value of the flash message.
     */
    public static function get(string $name): mixed
    {
        $name = "__$name";
        $message = Session::get($name);
        Session::forget($name);
        return $message;
    }

    /**
     * Check if flash messages exist for given keys.
     *
     * @param string|array $names The name or names of the flash message keys.
     * @return bool True if all flash messages exist, false otherwise.
     */
    public static function has(string|array $names): bool
    {
        if (is_array($names)) {
            foreach ($names as $name) {
                $name = "__$name";
                if (!Session::has($name)) {
                    return false;
                }
            }
            return true;
        }

        $name = "__$names";
        return Session::has($name);
    }

    /**
     * Keep a flash message for the next request.
     *
     * @param string $name The name of the flash message key.
     */
    public static function keep($name)
    {
        $value = self::get($name);  // Use get to retrieve the value
        if ($value !== null) {
            self::set($name, $value);  // Re-set the value to keep it
        }
    }
}
