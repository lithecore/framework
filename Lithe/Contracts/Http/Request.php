<?php

namespace Lithe\Contracts\Http;

interface Request
{

    /**
     * Retrieves the value of a specific header from the request.
     *
     * @param string $name The name of the desired header.
     * @param mixed $default The default value to return if the header does not exist.
     * @return mixed The value of the header if it exists, or the default value if it does not.
     */
    public function header(string $name, mixed $default = null): mixed;


    public function isAjax();

    /**
     * Filters a value based on the specified type.
     *
     * @param string $key The key that holds the value to be filtered.
     * @param string $filterType The type of filter to be applied.
     * @param mixed $default The default value to return if the filtering fails or the value is not set.
     * @return mixed The filtered value, or the default value if the filter is not supported or the value is invalid.
     */
    public function filter(string $key, string $filterType, $default = null);

    /**
     * Checks if the current URL matches the specified pattern.
     *
     * @param string $url The URL pattern to compare against the current URL.
     * @return bool Returns true if the current URL matches the pattern, otherwise false.
     */
    public function is(string $url): bool;

     /**
     * Validates input data against the provided rules.
     *
     * This method collects relevant input data using the specified rules
     * and returns a validator instance with the provided data and rules for further processing.
     *
     * @param array $rules An associative array where the key is the field name and the value is the validation rule.
     * @return \Lithe\Component\Validator Returns a validator instance configured with the provided data and rules.
     */
    public function validate(array $rules): \Lithe\Component\Validator;

    /**
     * Retrieves the entire request body or specific parts of it.
     *
     * @param array|null $keys An array of keys to retrieve specific parts of the body. If null, returns the entire body.
     * @param array|null $exclude An array of keys to exclude from the returned body.
     * @return mixed An associative array or an object containing the filtered request body data.
     */
    public function body(array $keys = null, array $exclude = null): mixed;

    /**
     * Retrieves a specific input value from the request body.
     *
     * @param string $key The key of the input value to retrieve.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed The value of the input if it exists, or the default value if it doesn't.
     */
    public function input(string $key, $default = null);

    /**
     * Checks if a specific input field or fields are present in the request data.
     *
     * @param string|array $key The key or an array of keys to check in the request data.
     * @return bool True if the input field(s) are present, otherwise false.
     */
    public function has(string|array $key): bool;

    /**
     * Checks if the HTTP request method matches the given method.
     *
     * @param string $method
     * @return bool
     */
    public function isMethod(string $method): bool;

    /**
     * Checks if the request expects a JSON response.
     *
     * @return bool
     */
    public function wantsJson(): bool;
    /**
     * Check if the request is secure (HTTPS).
     *
     * @return bool
     */
    public function secure(): bool;

    /**
     * Get the protocol of the request.
     *
     * @return string
     */
    public function protocol(): string;

    /**
     * Retrieves the host of the server.
     *
     * Constructs the host URL considering the server's protocol and host.
     *
     * @return string The host URL.
     */
    function getHost(): string;

    /**
     * Get the value of a specific cookie.
     *
     * @param string $name The name of the cookie.
     * @param mixed $default Default value to return if the cookie does not exist.
     * @return mixed The value of the cookie if it exists, otherwise the default value.
     */
    public function cookie(string $name, $default = null);

    /**
     * Gets a query parameter from the URL.
     *
     * @param string $key The name of the query parameter.
     * @param mixed $default The default value to return if the query parameter does not exist.
     * @return mixed The value of the query parameter if it exists, or the default value if it doesn't.
     */
    public function query(string $key = null, $default = null);

    /* Get information about an uploaded file.
    *
    * @param string $name The name of the file input.
    * @return \Lithe\Component\Upload|null Returns the file information if available, or null if not found.
    */
    public function file(string $name): \Lithe\Component\Upload|null;

    /**
     * Creates a new instance of the Session class.
     *
     * @return \Lithe\Support\Session A new instance of the Session class.
     */
    public function session(): \Lithe\Support\Session;
}
