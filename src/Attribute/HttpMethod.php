<?php

namespace Stein\Framework\Attribute;

use Attribute;

/**
 * Identifies an action that supports a given set of HTTP methods.
 */
#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class HttpMethod
{

    /** @var string[] $methods */
    public readonly array $methods;
    public readonly string $path;

    /**
     * @param string[] $methods
     * @param string $path
     */
    public function __construct(array $methods, string $path)
    {
        $this->methods = $methods;
        $this->path = trim($path, '/ ');
    }
}
