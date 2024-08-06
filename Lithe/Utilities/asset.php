<?php

/**
 * Generates the URL for an asset based on the server environment.
 *
 * Constructs a URL for the provided asset path considering the server's protocol,
 * host, and base directory of the script.
 *
 * @param string $path The path to the asset.
 * @return string The complete URL of the asset.
 */
function asset(string $path): string
{
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    $scheme = $isSecure ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if ($baseDir === '/') {
        $baseDir = '';
    }
    return $scheme . $host . $baseDir . '/' . ltrim($path, '/');
}
