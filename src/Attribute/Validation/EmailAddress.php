<?php

namespace Stein\Framework\Attribute\Validation;

use Attribute;

/**
 * Class EmailAddress
 *
 * Validates that a property value is a valid email address.
 *
 * @package Stein\Framework\Attribute\Validation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class EmailAddress extends ValidationAttribute
{

    public function __construct(
        public readonly string $email,
        public readonly string $error_message = 'Invalid email address'
    ) {}

    public function isValid(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) === $this->email;
    }
}
