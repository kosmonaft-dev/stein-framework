<?php

namespace Stein\Framework\Attribute\Validation;

use Attribute;

/**
 * Class MinLength
 *
 * Specifies the minimum length of a string or array property.
 *
 * @package Stein\Framework\Attribute\Validation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class MinLength extends ValidationAttribute
{

    public function __construct(
        public readonly int $min_length,
        public readonly string $error_message = 'Invalid value, minimum length not reached'
    ) {}

    public function isValid(mixed $value): bool
    {
        if (!is_string($value) && !is_array($value)) {
            return false;
        }

        return is_array($value) ?
            count($value) >= $this->min_length :
            strlen($value) >= $this->min_length;
    }
}
