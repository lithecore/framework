<?php

namespace Lithe\Support;

use RuntimeException;

/**
 * Component responsible for managing session.
 */
class Session
{
    /**
     * Set a session variable.
     *
     * @param string $name Name of the session variable.
     * @param mixed $value Value to be assigned to the session variable.
     */
    public static function put($name, $value)
    {
        self::checkSessionActive();
        $_SESSION[$name] = $value;
    }

    /**
     * Get the value of a session variable.
     *
     * @param string $name Name of the session variable.
     * @param mixed $default Default value to return if the session variable is not set.
     * @return mixed Value of the session variable or the default value if not set.
     */
    public static function get($name, $default = null)
    {
        self::checkSessionActive();
        return $_SESSION[$name] ?? $default;
    }

    /**
     * Unset a specific session variable or multiple session variables.
     *
     * @param mixed $name Name(s) of the session variable(s) to unset. Can be a string or an array of strings.
     */
    public static function forget($name)
    {
        self::checkSessionActive();

        if (is_array($name)) {
            foreach ($name as $item) {
                unset($_SESSION[$item]);
            }
        } elseif (is_string($name)) {
            unset($_SESSION[$name]);
        } else {
            throw new \InvalidArgumentException('The parameter should be a string or an array of strings.');
        }
    }

    /**
     * Destroy session variables if a session is active.
     */
    public static function destroy()
    {
        self::checkSessionActive();
        session_unset();
        session_destroy();
    }

    /**
     * Check if the session is active.
     *
     * @return bool
     */
    public static function isActive()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Regenerate the session ID.
     *
     * @param bool $deleteOldSession Whether to delete the old session or not.
     * @return bool True on success, false on failure.
     */
    public static function regenerate(bool $deleteOldSession = true)
    {
        self::checkSessionActive();
        return session_regenerate_id($deleteOldSession);
    }

    /**
     * Get the current session ID.
     *
     * @return string|false The session ID, or false if no session exists.
     */
    public static function getId()
    {
        self::checkSessionActive();
        return session_id();
    }

    /**
     * Set the session ID.
     *
     * @param string $sessionId The session ID to set.
     * @return bool True on success, false on failure.
     */
    public static function setId($sessionId)
    {
        self::checkSessionActive();
        return session_id($sessionId);
    }

    /**
     * Get all session variables.
     *
     * @return array Associative array of all session variables.
     */
    public static function all()
    {
        self::checkSessionActive();
        return $_SESSION;
    }

    /**
     * Check if session variables exist.
     *
     * @param string|array $names The name or names of the session variables.
     * @return bool True if all session variables exist, false otherwise.
     */
    public static function has(string|array $names)
    {
        self::checkSessionActive();

        if (is_array($names)) {
            foreach ($names as $name) {
                if (!isset($_SESSION[$name])) {
                    return false;
                }
            }
            return true;
        }

        return isset($_SESSION[$names]);
    }

    /**
     * Magic method to set a session variable using object property syntax.
     *
     * @param string $name Name of the session variable.
     * @param mixed $value Value to be assigned to the session variable.
     */
    public function __set($name, $value)
    {
        self::put($name, $value);
    }

    /**
     * Magic method to get the value of a session variable using object property syntax.
     *
     * @param string $name Name of the session variable.
     * @return mixed Value of the session variable or null if not set.
     */
    public function __get($name)
    {
        return self::get($name);
    }

    /**
     * Check if the session is active.
     *
     * @throws RuntimeException If the session is not active.
     */
    private static function checkSessionActive()
    {
        if (!self::isActive()) {
            throw new RuntimeException('Session is not active.');
        }
    }
}
