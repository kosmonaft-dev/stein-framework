<?php

namespace Stein\Framework\Attribute;

use Attribute;

/**
 * Identifies an action that supports a GET HTTP method.
 */
#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class HttpGet extends HttpMethod
{

    public function __construct(string $path = '')
    {
        parent::__construct(['GET'], $path);
    }
}
