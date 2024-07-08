<?php

namespace Stein\Framework\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RouteName
{

    public readonly string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}