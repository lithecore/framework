<?php

namespace Lithe\Component;

use Lithe\Support\Log;

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $error = [];
    protected array $errorCode = [
        'required' => 1001,
        'email' => 1002,
        'url' => 1003,
        'ip' => 1004,
        'number' => 1005,
        'integer' => 1006,
        'boolean' => 1007,
        'regex' => 1008,
        'min' => 1009,
        'max' => 1010,
        'range' => 1011,
        'dateFormat' => 1012,
        'alphanumeric' => 1013,
    ];

    /**
     * Constructor to initialize data and rules.
     *
     * @param array $data The data to validate.
     * @param array $rules The validation rules to apply.
     */
    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    /**
     * Validates the data against the provided rules.
     *
     * @return bool Returns true if all validations pass, false otherwise.
     */
    public function passed(): bool
    {
        foreach ($this->rules as $field => $rules) {
            $rules = explode('|', $rules);
            foreach ($rules as $rule) {
                $ruleParams = explode(':', $rule);
                $ruleName = $ruleParams[0];
                $params = $ruleParams[1] ?? '';

                $methodName = 'validate' . ucfirst($ruleName);
                if (method_exists($this, $methodName)) {
                    if (!$this->$methodName($field, $params)) {
                        $this->addError($field, $ruleName);
                    }
                } else {
                    $message = "Validation rule {$ruleName} not supported.";
                    Log::error($message);
                    throw new \Exception($message);
                }
            }
        }
        return empty($this->error);
    }

    /**
     * Adds an error code to the list of failed validations.
     *
     * @param string $field The field that failed validation.
     * @param string $rule The rule that failed.
     */
    protected function addError(string $field, string $rule): void
    {
        $code = $this->errorCode[$rule] ?? 9999; // 9999 for unknown errors
        $this->error[$field][] = $code;
    }

    /**
     * Returns the list of failed validations.
     *
     * @return array The array of failed validations.
     */
    public function errors(): array
    {
        return $this->error;
    }

    /**
     * Validates if the field value is a valid email.
     *
     * @param string $field The field name.
     * @param mixed $params Additional parameters (not used).
     * @return bool Returns true if the email is valid, false otherwise.
     */
    protected function validateEmail(string $field, $params): bool
    {
        return filter_var($this->data[$field], FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates if the field value is required and not empty.
     *
     * @param string $field The field name.
     * @param mixed $params Additional parameters (not used).
     * @return bool Returns true if the field is set and not empty, false otherwise.
     */
    protected function validateRequired(string $field, $params): bool
    {
        return isset($this->data[$field]) && !empty($this->data[$field]);
    }

    /**
     * Validates if the field value is a valid URL.
     *
     * @param string $field The field name.
     * @param mixed $params Additional parameters (not used).
     * @return bool Returns true if the URL is valid, false otherwise.
     */
    protected function validateUrl(string $field, $params): bool
    {
        return filter_var($this->data[$field], FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validates if the field value is a valid IP address.
     *
     * @param string $field The field name.
     * @param mixed $params Additional parameters (not used).
     * @return bool Returns true if the IP address is valid, false otherwise.
     */
    protected function validateIp(string $field, $params): bool
    {
        return filter_var($this->data[$field], FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validates if the field value is a valid number.
     *
     * @param string $field The field name.
     * @param mixed $params Additional parameters (not used).
     * @return bool Returns true if the value is numeric, false otherwise.
     */
    protected function validateNumber(string $field, $params): bool
    {
        return is_numeric($this->data[$field]);
    }

    /**
     * Validates if the field value is a valid integer.
     *
     * @param string $field The field name.
     * @param mixed $params Additional parameters (not used).
     * @return bool Returns true if the value is an integer, false otherwise.
     */
    protected function validateInteger(string $field, $params): bool
    {
        return filter_var($this->data[$field], FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validates if the field value is a boolean.
     *
     * @param string $field The field name.
     * @param mixed $params Additional parameters (not used).
     * @return bool Returns true if the value is a boolean, false otherwise.
     */
    protected function validateBoolean(string $field, $params): bool
    {
        return is_bool(filter_var($this->data[$field], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
    }

    /**
     * Validates if the field value matches a regular expression pattern.
     *
     * @param string $field The field name.
     * @param string $pattern The regular expression pattern.
     * @return bool Returns true if the value matches the pattern, false otherwise.
     */
    protected function validateRegex(string $field, $pattern): bool
    {
        return preg_match($pattern, $this->data[$field]) === 1;
    }

    /**
     * Validates if the field value has a minimum length.
     *
     * @param string $field The field name.
     * @param int $min The minimum length.
     * @return bool Returns true if the value meets the minimum length, false otherwise.
     */
    protected function validateMin(string $field, $min): bool
    {
        return strlen($this->data[$field]) >= (int)$min;
    }

    /**
     * Validates if the field value has a maximum length.
     *
     * @param string $field The field name.
     * @param int $max The maximum length.
     * @return bool Returns true if the value does not exceed the maximum length, false otherwise.
     */
    protected function validateMax(string $field, $max): bool
    {
        return strlen($this->data[$field]) <= (int)$max;
    }

    /**
     * Validates if the field value is within a specified range.
     *
     * @param string $field The field name.
     * @param string $params The minimum and maximum range, separated by a comma.
     * @return bool Returns true if the value is within the range, false otherwise.
     */
    protected function validateRange(string $field, $params): bool
    {
        [$min, $max] = explode(',', $params);
        $value = $this->data[$field];
        return is_numeric($value) && $value >= (int)$min && $value <= (int)$max;
    }

    /**
     * Validates if the field value matches a specified date format.
     *
     * @param string $field The field name.
     * @param string $format The date format.
     * @return bool Returns true if the date matches the format, false otherwise.
     */
    protected function validateDateFormat(string $field, $format): bool
    {
        $d = \DateTime::createFromFormat($format, $this->data[$field]);
        return $d && $d->format($format) === $this->data[$field];
    }

    /**
     * Validates if the field value is alphanumeric.
     *
     * @param string $field The field name.
     * @param mixed $params Additional parameters (not used).
     * @return bool Returns true if the value is alphanumeric, false otherwise.
     */
    protected function validateAlphanumeric(string $field, $params): bool
    {
        return ctype_alnum($this->data[$field]);
    }
}
