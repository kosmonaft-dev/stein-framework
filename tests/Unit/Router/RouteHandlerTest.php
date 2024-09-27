<?php

namespace P\Tests\Unit\Router;

use Laminas\Diactoros\Response\JsonResponse;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Mockery;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Stein\Framework\Router\{RouteHandler, RouteInterface};

class TempController
{
    public function index()
    {
        return new JsonResponse(['message' => 'Hello World!']);
    }
};

beforeEach(function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->response = Mockery::mock(ResponseInterface::class);
    $this->route = Mockery::mock(RouteInterface::class);
    $container = new Container();
    $container->delegate(new ReflectionContainer(true));
    $this->handler = new RouteHandler([TempController::class, 'index'], $container);
});

test('handles a request and returns a response', function () {
    $this->route->shouldReceive('getHandler')->andReturn(function ($request) {
        return $this->response;
    });

    $response = $this->handler->handle($this->request);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

afterEach(function () {
    Mockery::close();
});
