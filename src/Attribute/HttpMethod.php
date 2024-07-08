<?php

namespace Stein\Framework\Attribute;

use Attribute;

/**
 * Identifies an action that supports a given set of HTTP methods.
 */
#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class HttpMethod
{

    public readonly array $methods;
    public readonly string $path;

    public function __construct(array $methods, string $path)
    {
        $this->methods = $methods;
        $this->path = trim($path, '/ ');
    }
}
