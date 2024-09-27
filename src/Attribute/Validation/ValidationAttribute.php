<?php

namespace Stein\Framework\Attribute\Validation;

use Stein\Framework\Attribute\Validation\Exception\ValidationException;

abstract class ValidationAttribute
{

    public readonly string $error_message;

    abstract public function isValid(mixed $value): bool;

    public function validate(mixed $value): void
    {
        if (!$this->isValid($value)) {
            throw new ValidationException($this->error_message);
        }
    }
}
