<?php

namespace Lithe\Middleware;

use Closure;
use Exception;
use Lithe\Support\Log;

/**
 * Middleware responsible for managing session.
 *
 * @param array $options Options for session management.
 *   - 'lifetime' (int): Lifetime of the session in seconds (default: 2592000).
 *   - 'domain' (string|null): Domain for which the session cookie is valid (default: null).
 *   - 'secure' (bool): Indicates if the session cookie should only be sent over secure connections (default: false).
 *   - 'httponly' (bool): Indicates if the session cookie should be accessible only through HTTP requests (default: true).
 *   - 'samesite' (string): SameSite attribute for session cookie to prevent CSRF attacks (default: 'Lax').
 *   - 'path' (string): Directory path where session files will be stored (default: 'storage/framework/session').
 * @return \Closure Middleware function that handles session management.
 */
function session(array $options = [])
{
    // Default options
    $defaultOptions = [
        'lifetime' => 2592000,
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
        'path' => PROJECT_ROOT. 'storage/framework/session',
    ];

    // Merge user options with default options
    $options = array_merge($defaultOptions, $options);

    return function (\Lithe\Contracts\Http\Request $req, \Lithe\Contracts\Http\Response $res, Closure $next) use ($options) {
        // Validar o caminho antes de tentar criar o diretório
        $savePath = realpath($options['path']) ?: $options['path'];

        // Start session if not already active
        try {
            // Check if session is not already active
            if (session_status() !== PHP_SESSION_ACTIVE) {
                // Verifica se o diretório de salvamento existe, senão cria
                if (!file_exists($savePath)) {
                    mkdir($savePath, 0755, true);
                }

                // Verifica se o diretório foi criado com sucesso
                if (!is_dir($savePath)) {
                    throw new \RuntimeException('Failed to create session save path directory.');
                }

                // Configura o PHP para usar o diretório especificado para salvar as sessões
                session_save_path($savePath);
                $lifetime = $options['lifetime'];

                // Session lifetime configuration
                ini_set("session.gc_maxlifetime", $lifetime);
                ini_set("session.cookie_lifetime", $lifetime);

                // Session cookie configuration
                session_set_cookie_params([
                    'lifetime' => $lifetime,
                    'path' => '/',
                    'domain' => $options['domain'],
                    'secure' => $options['secure'],
                    'httponly' => $options['httponly'],
                    'samesite' => $options['samesite'],
                ]);

                // Start the session
                if (session_start() === false) {
                    throw new Exception('Failed to start the session.');
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            Log::error($e->getMessage());
        }

        // Assign session object to request
        $req->session = new \Lithe\Support\Session;

        $next();
    };
}