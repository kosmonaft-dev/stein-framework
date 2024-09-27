<?php

namespace Stein\Framework\Attribute\Validation;

use Attribute;

/**
 * Class Required
 *
 * Ensures that a property has a value.
 *
 * @package Stein\Framework\Attribute\Validation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Required extends ValidationAttribute
{

    public function __construct(
        public readonly bool $allow_empty = false,
        public readonly string $error_message = 'Value is required'
    ) {}

    public function isValid(mixed $value): bool
    {
        if ($this->allow_empty) {
            return !empty($value);
        }

        return $value !== null;
    }
}
