<?php

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Http\Middleware\RouteMiddleware;
use Stein\Framework\Router\RouteHandler;
use Stein\Framework\Router\RouteResultInterface;
use Stein\Framework\Router\RouterInterface;

beforeEach(function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->handler = Mockery::mock(RequestHandlerInterface::class);
    $this->router = Mockery::mock(RouterInterface::class);
    $this->container = Mockery::mock(ContainerInterface::class);
    $this->routeResult = Mockery::mock(RouteResultInterface::class);
    $this->middleware = new RouteMiddleware($this->router, $this->container);
});

test('passes request to next handler if route result is not successful', function () {
    $this->router->shouldReceive('match')->once()->with($this->request)->andReturn($this->routeResult);
    $this->routeResult->shouldReceive('isSuccess')->once()->andReturn(false);
    $this->request->shouldReceive('withAttribute')->once()->with(RouteResultInterface::class, $this->routeResult)->andReturnSelf();
    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});
