<?php

namespace Stein\Framework\Application;

use Psr\Http\Message\ServerRequestInterface;

interface ApplicationInterface
{

    /**
     * Run the application.
     * @param ServerRequestInterface $server_request
     * @return void
     */
    public function run(ServerRequestInterface $server_request): void;
}