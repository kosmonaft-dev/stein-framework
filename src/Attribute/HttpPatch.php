<?php

namespace Stein\Framework\Attribute;

use Attribute;

/**
 * Identifies an action that supports a PATCH HTTP method.
 */
#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class HttpPatch extends HttpMethod
{

    public function __construct(string $path = '')
    {
        parent::__construct(['PATCH'], $path);
    }
}
