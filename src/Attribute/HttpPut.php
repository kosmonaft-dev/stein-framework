<?php

namespace Stein\Framework\Attribute;

use Attribute;

/**
 * Identifies an action that supports a PUT HTTP method.
 */
#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class HttpPut extends HttpMethod
{

    public function __construct(string $path = '')
    {
        parent::__construct(['PUT'], $path);
    }
}
