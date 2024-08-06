<?php

namespace Lithe\Middleware\Security;

use Lithe\Support\Log;
use Lithe\Support\Session;

/**
 * Middleware responsible for managing CSRF (Cross-Site Request Forgery) tokens.
 *
 * @param array $options Options for CSRF token management.
 *   - 'name' (string): The name of the CSRF token (default: '_token').
 *   - 'expire' (int): The expiration time for the CSRF token in seconds (default: 600).
 *   - 'checkBody' (bool): Flag to check the token in the request body (default: false).
 *   - 'bodyMethods' (array): An array of HTTP methods where token validation should be applied (default: ['POST']).
 *   - 'regenerate' (bool): Flag to determine if the token should be regenerated on every request (default: false).
 * @return \Closure Middleware function that handles CSRF token validation.
 */
function csrf(array $options = [])
{
    // Default options
    $defaultOptions = [
        'name' => '_token',
        'expire' => 600,
        'checkBody' => false,
        'bodyMethods' => ['POST'],
        'regenerate' => false,
    ];

    // Merge default options with provided ones
    $options = array_merge($defaultOptions, $options);

    // Extract options to individual variables
    extract($options);

    /**
     * @param \Lithe\Contracts\Http\Request $req
     * @param \Lithe\Contracts\Http\Response $res
     * @param callable $next
     */
    return function (\Lithe\Contracts\Http\Request $req, \Lithe\Contracts\Http\Response $res, $next) use ($name, $expire, $checkBody, $bodyMethods, $regenerate) {
        $csrf = new class($req, $name, $expire, $checkBody, $regenerate)
        {
            private $name;
            private $expire;
            private $req;
            private $checkBody;
            private $regenerate;

            /**
             * Csrf constructor.
             *
             * @param \Lithe\Contracts\Http\Request $req
             * @param string $name The name of the CSRF token.
             * @param int $expire The expiration time for the CSRF token in seconds.
             * @param bool $checkBody Flag to check the token in the request body.
             * @param bool $regenerate Flag to regenerate the token on every request.
             */
            public function __construct(\Lithe\Contracts\Http\Request $req, string $name, $expire, $checkBody, $regenerate)
            {
                $this->name = $name;
                $this->req = $req;
                $this->expire = $expire;
                $this->checkBody = $checkBody;
                $this->regenerate = $regenerate;
                $this->generateToken();
            }

            /**
             * Generates a new CSRF token and returns it.
             *
             * @param bool $force Flag to indicate whether to force generation of a new token.
             * @return string The generated CSRF token.
             */
            public function generateToken(bool $force = false): string
            {
                $name = $this->name;
                $time = $name . '_time';

                try {
                    $sessionToken = Session::get($name);
                    if ($force || !$sessionToken) {
                        $token = bin2hex(random_bytes(32));
                        Session::put($name, $token);
                        Session::put($time, time());
                        return $token;
                    }
                    return $sessionToken;
                } catch (\Exception $e) {
                    Log::error($e);
                    return '';
                }
            }

            /**
             * Gets the CSRF token.
             *
             * @return string The CSRF token.
             */
            public function getToken(): string
            {
                $name = $this->name;
                return Session::get($name, '');
            }

            /**
             * Verifies if the provided CSRF token is valid.
             *
             * @param string $token The CSRF token to be verified.
             * @param bool $checkBody If true, checks the token in the request body; otherwise, checks in the session.
             * @return bool Returns true if the token is valid; otherwise, returns false.
             */
            public function verifyToken(string $token, bool $checkBody = false): bool
            {
                $name = $this->name;
                $sessionToken = Session::get($name);
                $tokenField = $this->req->input($name);

                // Check if the session token is fresh
                if (!$this->isTokenFresh()) {
                    $this->generateToken(true); // Regenerate token if expired
                    // Validate new token
                    return $this->validateToken($sessionToken, $token, $tokenField, $checkBody);
                }

                // Validate token when fresh
                return $this->validateToken($sessionToken, $token, $tokenField, $checkBody);
            }

            /**
             * Validates the CSRF token.
             *
             * @param string|null $sessionToken The token stored in the session.
             * @param string $token The CSRF token to be verified.
             * @param mixed $tokenField The token from the request body.
             * @param bool $checkBody Whether to check the token in the request body.
             * @return bool Returns true if the token is valid; otherwise, returns false.
             */
            private function validateToken(?string $sessionToken, string $token, $tokenField, bool $checkBody): bool
            {
                if ($checkBody) {
                    if ($tokenField !== null && hash_equals($sessionToken, $tokenField)) {
                        // Regenerate token for next request if required
                        $this->generateToken($this->regenerate);
                        return true;
                    }
                } else {
                    if (hash_equals($sessionToken, $token)) {
                        // Regenerate token for next request if required
                        $this->generateToken($this->regenerate);
                        return true;
                    }
                }

                return false;
            }

            /**
             * Checks if the current CSRF token is still valid in terms of expiration time.
             *
             * @return bool Returns true if the token is still valid, otherwise returns false.
             */
            private function isTokenFresh(): bool
            {
                $time = $this->name . '_time';
                $tokenTime = Session::get($time) ?? 0;
                return (time() - $tokenTime) < $this->expire;
            }

            /**
             * Gets a hidden form field containing the CSRF token.
             *
             * @return string A string containing a hidden form field with the CSRF token.
             */
            public function getTokenField(): string
            {
                $token = $this->getToken();
                return '<input type="hidden" name="' . htmlspecialchars($this->name, ENT_QUOTES) . '" value="' . $token . '">';
            }

            /**
             * Destroys the CSRF token and its associated time variable in the session.
             */
            public function invalidate(): void
            {
                $name = $this->name;
                $time = $name . '_time';
                if (Session::has($name)) {
                    Session::forget([$name, $time]);
                }
            }

            /**
             * Checks if the CSRF token exists in the session.
             *
             * @return bool Returns true if the token exists, otherwise returns false.
             */
            public function exists(): bool
            {
                return Session::has($this->name) && $this->isTokenFresh();
            }
        };

        // Check CSRF token automatically if $checkBody is true and method is in $bodyMethods
        if ($checkBody && in_array($req->method, $bodyMethods)) {
            $token = $req->input($name, '');

            if (!$csrf->verifyToken($token, true)) {
                throw new \Lithe\Contracts\Http\HttpException(419, 'PAGE EXPIRED');
            }
        }

        // Inject CSRF object into request
        $req->csrf = $csrf;

        $next();
    };
}
