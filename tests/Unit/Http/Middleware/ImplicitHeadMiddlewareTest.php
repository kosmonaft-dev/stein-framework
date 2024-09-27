<?php

namespace P\Tests\Unit\Http\Middleware;

use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Http\Middleware\ImplicitHeadMiddleware;
use Stein\Framework\Router\RouteResultInterface;
use Stein\Framework\Router\RouterInterface;

beforeEach(function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->handler = Mockery::mock(RequestHandlerInterface::class);
    $this->router = Mockery::mock(RouterInterface::class);
    $this->middleware = new ImplicitHeadMiddleware($this->router);
});

test('passes request to next handler if method is not HEAD', function () {
    $this->request->shouldReceive('getMethod')->once()->andReturn('GET');
    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('passes request to next handler if no route result', function () {
    $this->request->shouldReceive('getMethod')->once()->andReturn('HEAD');
    $this->request->shouldReceive('getAttribute')->once()->with(RouteResultInterface::class)->andReturn(null);
    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('passes request to next handler if route is matched', function () {
    $routeResult = Mockery::mock(RouteResultInterface::class);
    $routeResult->shouldReceive('getMatchedRoute')->once()->andReturn(true);

    $this->request->shouldReceive('getMethod')->once()->andReturn('HEAD');
    $this->request->shouldReceive('getAttribute')->once()->with(RouteResultInterface::class)->andReturn($routeResult);
    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});
