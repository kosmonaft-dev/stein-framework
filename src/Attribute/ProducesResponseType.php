<?php

namespace Stein\Framework\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class ProducesResponseType
{

    public readonly string $response_type;
    public readonly int $status_code;

    public function __construct(string $response_type = '', int $status_code = 200)
    {
        $this->response_type = $response_type;
        $this->status_code = $status_code;
    }
}
