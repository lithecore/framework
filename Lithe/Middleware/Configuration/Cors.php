<?php

namespace Lithe\Middleware\Configuration;

/**
 * Configures Cross-Origin Resource Sharing (CORS) headers.
 *
 * @param array $options Options for CORS configuration.
 *   - 'origins' (string|array): Allowed origins (default: '*').
 *   - 'methods' (string): Allowed HTTP methods (default: 'GET, POST, OPTIONS').
 *   - 'headers' (string|array): Allowed headers (default: 'Origin, X-Requested-With, Content-Type, Accept').
 *   - 'credentials' (bool): Indicates whether credentials are allowed (default: true).
 *   - 'maxAge' (int): Indicates how long the results of a preflight request can be cached (default: null).
 * @return \Closure Middleware
 */
function cors(array $options = [])
{
    /**
     *
     * @param \Lithe\Contracts\Http\Request $req
     * @param \Lithe\Contracts\Http\Response $res
     * @param callable $next
     */
    return function (\Lithe\Contracts\Http\Request $req, \Lithe\Contracts\Http\Response $res, callable $next) use ($options) {
        // Default CORS settings
        $defaults = [
            'origins' => '*',
            'methods' => 'GET, POST, OPTIONS',
            'headers' => 'Origin, X-Requested-With, Content-Type, Accept',
            'credentials' => true,
            'maxAge' => null,
        ];

        // Merge default settings with provided options
        $settings = array_merge($defaults, $options);

        // Set allowed origins
        $allowedOrigins = is_array($settings['origins']) ? implode(', ', $settings['origins']) : $settings['origins'];
        $res->setHeader('Access-Control-Allow-Origin', $allowedOrigins);

        // Set allowed methods
        if (!empty($settings['methods'])) {
            $res->setHeader('Access-Control-Allow-Methods', $settings['methods']);
        }

        // Set allowed headers
        if (!empty($settings['headers'])) {
            $allowedHeaders = is_array($settings['headers']) ? implode(', ', $settings['headers']) : $settings['headers'];
            $res->setHeader('Access-Control-Allow-Headers', $allowedHeaders);
        }

        // Set whether credentials are allowed
        if ($settings['credentials']) {
            $res->setHeader('Access-Control-Allow-Credentials', 'true');
        }

        // Set max age for preflight request caching
        if (isset($settings['maxAge'])) {
            $res->setHeader('Access-Control-Max-Age', $settings['maxAge']);
        }

        // Handle OPTIONS request for preflight
        if ($req->method === 'OPTIONS') {
            $res->type('text/plain') // Optional: Set content type
                ->setHeader('HTTP/1.1 200 OK') // Optional: Not needed, handled by status code
                ->end(); // End the response
            return; // Return to prevent further processing
        }

        // Proceed to the next middleware
        $next();
    };
}
