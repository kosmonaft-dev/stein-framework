<?php

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Http\Middleware\ImplicitOptionsMiddleware;
use Stein\Framework\Router\RouteResultInterface;

beforeEach(function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->handler = Mockery::mock(RequestHandlerInterface::class);
    $this->routeResult = Mockery::mock(RouteResultInterface::class);
    $this->middleware = new ImplicitOptionsMiddleware();
});

test('passes request to next handler if method is not OPTIONS', function () {
    $this->request->shouldReceive('getMethod')->once()->andReturn('GET');
    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('passes request to next handler if no route result', function () {
    $this->request->shouldReceive('getMethod')->once()->andReturn('OPTIONS');
    $this->request->shouldReceive('getAttribute')->once()->with(RouteResultInterface::class)->andReturn(null);
    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('passes request to next handler if route result is failure and not method failure', function () {
    $this->request->shouldReceive('getMethod')->once()->andReturn('OPTIONS');
    $this->request->shouldReceive('getAttribute')->once()->with(RouteResultInterface::class)->andReturn($this->routeResult);
    $this->routeResult->shouldReceive('isFailure')->once()->andReturn(true);
    $this->routeResult->shouldReceive('isMethodFailure')->once()->andReturn(false);
    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('passes request to next handler if route is matched', function () {
    $this->request->shouldReceive('getMethod')->once()->andReturn('OPTIONS');
    $this->request->shouldReceive('getAttribute')->once()->with(RouteResultInterface::class)->andReturn($this->routeResult);
    $this->routeResult->shouldReceive('isFailure')->once()->andReturn(false);
    $this->routeResult->shouldReceive('getMatchedRoute')->once()->andReturn(true);
    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});
