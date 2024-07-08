<?php

namespace Stein\Framework\Attribute;

use Attribute;

/**
 * Identifies an action that supports a POST HTTP method.
 */
#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class HttpPost extends HttpMethod
{

    public function __construct(string $path = '')
    {
        parent::__construct(['POST'], $path);
    }
}
