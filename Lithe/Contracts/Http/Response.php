<?php

namespace Lithe\Contracts\Http;

interface Response
{
    /**
     * Renders a view.
     *
     * @param string $file The name of the view file.
     * @param array|null $data Data to pass to the view.
     */
    public function render(string $file, ?array $data = []): void;

    /**
     * Renders a view.
     *
     * @param string $file The name of the view file.
     * @param array|null $data Data to pass to the view.
     */
    public function view(string $file, ?array $data = []): void;

    /**
     * Sends a response, which can be serialized JSON data.
     *
     * @param mixed $data Data to be sent as response.
     */
    public function send(mixed $data): void;

    /**
     * Redirects to a location using an HTTP redirect.
     *
     * @param string $url URL to redirect to.
     * @param bool $permanent Is this a permanent redirect? (default is false).
     * @return void
     */
    public function redirect(string $url, bool $permanent = false): void;

    /**
     * Sends a response in JSON format.
     *
     * @param mixed $data Data to be sent as JSON response.
     */
    public function json(mixed $data): void;

    /**
     * Sets the HTTP status code for the response.
     *
     * @param int $statusCode HTTP status code.
     * @return self Current Response object for chaining.
     */
    public function status(int $statusCode): self;

    /**
     * Retorna o código de status HTTP atual da resposta.
     *
     * @return int|null Código de status HTTP atual.
     */
    public function getStatusCode(): ?int;

    /**
     * Sets an HTTP header in the response.
     *
     * @param string $name Name of the header.
     * @param string|null $value Value of the header.
     * @return self Current Response object for chaining.
     */
    public function setHeader(string $name, ?string $value = null): self;

    /**
     * Ends the response.
     */
    public function end(?string $message = null): void;

    /**
     * Sends a file for download.
     *
     * @param string $file Path to the file.
     * @param string|null $name Name of the file for download.
     * @param array $headers Additional headers.
     * @return void
     */
    public function download(string $file, ?string $name = null, array $headers = []);

    /**
     * Sets multiple headers at once.
     *
     * @param array $headers Associative array of headers.
     * @return self Current Response object for chaining.
     */
    public function setHeaders(array $headers): self;

    /**
     * Display a file in the browser.
     *
     * @param string $file Path to the file.
     * @param array $headers Additional headers.
     * @return void
     */
    public function file(string $file, array $headers = []): void;

    /**
     * Set a new cookie.
     *
     * @param string $name The name of the cookie.
     * @param mixed $value The value of the cookie.
     * @param array $options Options to configure the cookie (default: []).
     *   - 'expire' (int): Expiration time of the cookie in seconds from the current time (default: 0).
     *   - 'path' (string): Path on the server where the cookie will be available (default: '/').
     *   - 'domain' (string): The domain for which the cookie is available (default: null).
     *   - 'secure' (bool): Indicates if the cookie should be transmitted only over a secure HTTPS connection (default: false).
     *   - 'httponly' (bool): When true, the cookie can only be accessed through HTTP protocol (default: true).
     * @return \Lithe\Contracts\Http\Response Returns the Response object for method chaining.
     * @throws \RuntimeException If headers have already been sent.
     */
    public function cookie(string $name, $value, array $options = []): \Lithe\Contracts\Http\Response;

    /**
     * Remove a cookie.
     *
     * @param string $name The name of the cookie to be removed.
     * @return \Lithe\Contracts\Http\Response
     */
    public function clearCookie(string $name): \Lithe\Contracts\Http\Response;

    /**
     * Sets the MIME type for the response.
     *
     * @param string $mimeType The MIME type to set for the response.
     * @return self The current Response object for method chaining.
     */
    public function type(string $mimeType): self;
}
