<?php

namespace Stein\Framework\Attribute\Validation;

use Attribute;

/**
 * Class RegularExpression
 *
 * Validates that a property value matches a specified regular expression.
 *
 * @package Stein\Framework\Attribute\Validation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class RegularExpression extends ValidationAttribute
{

    public function __construct(
        public readonly string $pattern,
        public readonly string $error_message = 'Invalid value'
    ) {}

    public function isValid(mixed $value): bool
    {
        return preg_match($this->pattern, $value) === 1;
    }
}
