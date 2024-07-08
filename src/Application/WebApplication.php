<?php

namespace Stein\Framework\Application;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class WebApplication implements ApplicationInterface
{

    public function __construct(
        protected RequestHandlerInterface $request_handler
    ) {}

    public function run(ServerRequestInterface $server_request): void
    {
        $response = $this->request_handler->handle($server_request);

        $emitter = new Emitter();
        $emitter->emit($response);
    }
}
