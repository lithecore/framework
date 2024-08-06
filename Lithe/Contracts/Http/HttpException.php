<?php

namespace Lithe\Contracts\Http;

/**
 * Exception thrown when an invalid parameter type is encountered.
 */
class HttpException extends \Exception
{
    // Status code for the exception (e.g., 404, 500, etc.)
    protected $statusCode;

    /**
     * Constructor.
     *
     * @param int $statusCode The HTTP status code.
     * @param string $message The exception message.
     * @param int $code The exception code.
     * @param \Throwable|null $previous The previous exception.
     */
    public function __construct($statusCode, $message = '', $code = 0, \Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the HTTP status code.
     *
     * @return int The HTTP status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
