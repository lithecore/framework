<?php

namespace Lithe\Support;

/**
 * Import class to include PHP files and add external variables.
 */
class Import
{
    private static $vars = [];
    private $directory;
    private $exceptions = [];

    /**
     * Specifies external variables to be added to the scope of each included file.
     *
     * @param array $vars Array of external variables.
     * @return Import
     */
    public static function with(array $vars = [])
    {
        self::$vars = $vars;
        return new self();
    }

    /**
     * Specifies the directory path for including files.
     *
     * @param string $directory Directory path.
     * @return Import
     */
    public static function dir(string $directory)
    {
        $instance = new self();
        $instance->directory = $directory;
        return $instance;
    }

    /**
     * Specifies an array of files to exclude from inclusion.
     *
     * @param array $exceptions Array of files to exclude.
     * @return $this
     */
    public function exceptions(array $exceptions)
    {
        $dir = is_dir($this->directory) ? $this->directory . '/' : '';
        $this->exceptions = array_map(function ($exception) use ($dir) {
            return $dir . $exception;
        }, $exceptions);
        return $this;
    }

    /**
     * Includes a single PHP file and returns its result.
     *
     * @param string $file Path of the file to include.
     * @return mixed Return value of the included file.
     */
    public static function file(string $file)
    {
        if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            extract(self::$vars);
            return include $file;
        }
    }

    /**
     * Includes all PHP files from the specified directory and its subdirectories.
     *
     * @return void
     */
    public function files()
    {
        $dir = $this->directory;

        if (!is_dir($dir)) {
            return;
        }

        $files = $this->scanDirectory($dir);
        foreach ($files as $file) {
            if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php' && !in_array($file, $this->exceptions)) {
                extract(self::$vars);
                include $file;
            }
        }
    }

    /**
     * Recursively scans a directory for PHP files, excluding exceptions.
     *
     * @param string $directory Path of the directory to scan.
     * @return array List of PHP files.
     */
    private function scanDirectory(string $directory)
    {
        $files = [];
        $items = array_diff(scandir($directory), ['.', '..']);
        foreach ($items as $item) {
            $path = "$directory/$item";
            if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $files[] = $path;
            } elseif (is_dir($path)) {
                $files = array_merge($files, $this->scanDirectory($path));
            }
        }
        return $files;
    }
}
