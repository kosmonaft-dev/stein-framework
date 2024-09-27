<?php

namespace Stein\Framework\Attribute\Validation;

use Attribute;

/**
 * Class Range
 *
 * Validates that a property value falls within a specified range.
 *
 * @package Stein\Framework\Attribute\Validation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Range extends ValidationAttribute
{

    public function __construct(
        public readonly int|float $min,
        public readonly int|float $max,
        public readonly string $error_message = 'Value is out of range'
    ) {}

    public function isValid(mixed $value): bool
    {
        return $value >= $this->min && $value <= $this->max;
    }
}
