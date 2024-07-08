<?php

namespace Stein\Framework\Attribute;

use Attribute;

/**
 * Identifies an action that supports a DELETE HTTP method.
 */
#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class HttpDelete extends HttpMethod
{

    public function __construct(string $path = '')
    {
        parent::__construct(['DELETE'], $path);
    }
}
