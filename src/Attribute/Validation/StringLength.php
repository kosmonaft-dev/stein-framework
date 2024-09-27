<?php

namespace Stein\Framework\Attribute\Validation;

use Attribute;

/**
 * Class StringLength
 *
 * Specifies the minimum and maximum length of a string property.
 *
 * @package Stein\Framework\Attribute\Validation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class StringLength extends ValidationAttribute
{

    function __construct(
        public readonly int $max,
        public readonly int $min = 0,
        public readonly string $error_message = 'Invalid string length'
    ) {}

    public function isValid(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $length = strlen($value);

        return $length >= $this->min && $length <= $this->max;
    }
}
