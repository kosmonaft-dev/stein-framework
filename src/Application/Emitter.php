<?php

namespace Stein\Framework\Application;

use Psr\Http\Message\ResponseInterface;

class Emitter
{

    /**
     * @param ResponseInterface $response
     * @link https://github.com/http-interop/response-sender/blob/master/src/functions.php
     */
    public function emit(ResponseInterface $response): void
    {
        $http_line = sprintf('HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        header($http_line, true, $response->getStatusCode());

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        $stream = $response->getBody();

        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        $length = 1024 * 8;
        while (!$stream->eof()) {
            echo $stream->read($length);
        }
    }
}
