<?php

namespace P\Tests\Unit\Http\Middleware;

use Mockery;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Http\Middleware\NotFoundMiddleware;

beforeEach(function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->handler = Mockery::mock(RequestHandlerInterface::class);
    $this->middleware = new NotFoundMiddleware();
});

test('returns 404 response', function () {
    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->getStatusCode())->toBe(404);
});
