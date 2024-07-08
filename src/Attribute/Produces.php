<?php

namespace Stein\Framework\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
class Produces
{

    public readonly string $content_type;

    /** @var string[]  */
    public readonly array $additional_content_types;

    public function __construct(string $content_type, array $additional_content_types = [])
    {
        $this->content_type = $content_type;
        $this->additional_content_types = array_map('strval', $additional_content_types);
    }
}
