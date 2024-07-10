<?php

namespace Stein\Framework\Http\Error;

use Exception;

class ApiCallException extends Exception
{

    public int $status_code = 500;
    public string $reason_phrase = 'Internal Server Error';
}
