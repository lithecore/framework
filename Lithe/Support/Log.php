<?php

namespace Lithe\Support;

use RuntimeException;

if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}

class Log
{
    const LOG_DIR = PROJECT_ROOT . 'storage/logs';
    const LOG_LEVELS = ['info', 'warning', 'error'];

    /**
     * Ensures the log directory exists.
     *
     * @throws RuntimeException If the directory cannot be created.
     */
    private static function ensureLogDirExists()
    {
        if (!is_dir(self::LOG_DIR)) {
            if (!mkdir(self::LOG_DIR, 0777, true) && !is_dir(self::LOG_DIR)) {
                throw new RuntimeException("Failed to create log directory: " . self::LOG_DIR);
            }
        }
    }

    /**
     * Logs a message to the specified log file.
     *
     * @param string $level The log level (info, warning, error).
     * @param string $message The message to log.
     * @throws RuntimeException If the log level is invalid.
     */
    private static function log(string $level, string $message): void
    {
        if (!in_array($level, self::LOG_LEVELS)) {
            throw new RuntimeException("Invalid log level: $level");
        }

        // Ensure the log directory exists
        self::ensureLogDirExists();

        // Path to the log file
        $logFile = self::LOG_DIR . "/$level.log";

        // Get the current time
        $currentTime = date('Y-m-d H:i:s');

        // Format the log message
        $logMessage = "[{$currentTime}] " . strtoupper($level) . ": {$message}\n";

        // Append the log message to the log file
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    /**
     * Logs an informational message.
     *
     * @param string $message The message to log.
     */
    public static function info(string $message): void
    {
        self::log('info', $message);
    }

    /**
     * Logs a warning message.
     *
     * @param string $message The message to log.
     */
    public static function warning(string $message): void
    {
        self::log('warning', $message);
    }

    /**
     * Logs an error message.
     *
     * @param string $message The message to log.
     */
    public static function error(string $message): void
    {
        self::log('error', $message);
    }
}
