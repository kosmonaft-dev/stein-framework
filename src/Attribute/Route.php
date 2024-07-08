<?php

namespace Stein\Framework\Attribute;

use Attribute;

/**
 * Specifies the route for an action in a controller.
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class Route
{

    public readonly string $path;
    public readonly int $priority;

    public function __construct(string $path, int $priority = 0)
    {
        $this->path = trim($path, '/ ');
        $this->priority = $priority;
    }
}
