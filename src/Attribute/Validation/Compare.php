<?php

namespace Stein\Framework\Attribute\Validation;

use Attribute;

/**
 * Class Compare
 *
 * Compares two properties for equality.
 *
 * @package Stein\Framework\Attribute\Validation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Compare extends ValidationAttribute
{

    public function __construct(
        public readonly string $other_property,
        public readonly string $error_message = 'Invalid value'
    ) {}

    public function isValid(mixed $value): bool
    {
        return $value === $this->other_property;
    }
}
