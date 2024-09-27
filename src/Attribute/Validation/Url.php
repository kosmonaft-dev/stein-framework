<?php

namespace Stein\Framework\Attribute\Validation;

use Attribute;

/**
 * Class Url
 *
 * Validates that a property value is a valid URL
 *
 * @package Stein\Framework\Attribute\Validation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Url extends ValidationAttribute
{

    public function __construct(
        public readonly string $url,
        public readonly string $error_message = 'Invalid URL'
    ) {}

    public function isValid(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) === $value;
    }
}
