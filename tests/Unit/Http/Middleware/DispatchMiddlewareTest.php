<?php

namespace P\Tests\Unit\Http\Middleware;

use Mockery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Http\Middleware\DispatchMiddleware;
use Stein\Framework\Router\RouteHandler;
use Stein\Framework\Router\RouteResultInterface;

beforeEach(function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->handler = Mockery::mock(RequestHandlerInterface::class);
    $this->routeHandler = Mockery::mock(RouteHandler::class);
    $this->routeResult = Mockery::mock(RouteResultInterface::class);
    $this->middleware = new DispatchMiddleware();

    $this->request->shouldReceive('getAttribute')->with(RouteHandler::class)->andReturn($this->routeHandler);
    $this->request->shouldReceive('getAttribute')->with(RouteResultInterface::class)->andReturn($this->routeResult);
    $this->routeResult->shouldReceive('getMatchedParams')->andReturn(['param1' => 'value1', 'param2' => 'value2']);
});

test('processes request with matched route', function () {
    $this->request->shouldReceive('withAttribute')->with('param1', 'value1')->andReturnSelf();
    $this->request->shouldReceive('withAttribute')->with('param2', 'value2')->andReturnSelf();
    $this->routeHandler->shouldReceive('handle')->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('passes request to next handler if no route result', function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->request->shouldReceive('getAttribute')->with(RouteHandler::class)->andReturn(null);
    $this->request->shouldReceive('getAttribute')->with(RouteResultInterface::class)->andReturn(null);
    $this->handler->shouldReceive('handle')->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('passes request to next handler if no route handler', function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->request->shouldReceive('getAttribute')->with(RouteHandler::class)->andReturn(null);
    $this->request->shouldReceive('getAttribute')->with(RouteResultInterface::class)->andReturn($this->routeResult);
    $this->handler->shouldReceive('handle')->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});
