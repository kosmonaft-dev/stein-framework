<?php

namespace Stein\Framework\Attribute\Validation;

use Attribute;

/**
 * Class MaxLength
 *
 * Specifies the maximum length of a string or array property.
 *
 * @package Stein\Framework\Attribute\Validation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class MaxLength extends ValidationAttribute
{

    public function __construct(
        public readonly int $max_length,
        public readonly string $error_message = 'Invalid value, maximum length exceeded'
    ) {}

    public function isValid(mixed $value): bool
    {
        if (!is_string($value) && !is_array($value)) {
            return false;
        }

        return is_array($value) ?
            count($value) <= $this->max_length :
            strlen($value) <= $this->max_length;
    }
}
