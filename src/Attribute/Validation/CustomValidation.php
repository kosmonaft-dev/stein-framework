<?php

namespace Stein\Framework\Attribute\Validation;

use Attribute;
use Closure;

/**
 * Class CustomValidation
 *
 * Allows for custom validation logic using a specified method.
 *
 * @package Stein\Framework\Attribute\Validation
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class CustomValidation extends ValidationAttribute
{

    public function __construct(
        public readonly Closure $callback,
        public readonly string $error_message = 'Invalid value'
    ) {}

    public function isValid(mixed $value): bool
    {
        return call_user_func($this->callback, $value);
    }
}
