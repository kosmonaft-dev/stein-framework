<?php

namespace P\Tests\Unit\Http\Middleware;

use Mockery;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Http\Middleware\MethodNotAllowedMiddleware;
use Stein\Framework\Router\RouteResultInterface;

beforeEach(function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->handler = Mockery::mock(RequestHandlerInterface::class);
    $this->routeResult = Mockery::mock(RouteResultInterface::class);
    $this->middleware = new MethodNotAllowedMiddleware();
});

test('passes request to next handler if no route result', function () {
    $this->request->shouldReceive('getAttribute')->once()->with(RouteResultInterface::class)->andReturn(null);
    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('passes request to next handler if route result is not method failure', function () {
    $this->request->shouldReceive('getAttribute')->once()->with(RouteResultInterface::class)->andReturn($this->routeResult);
    $this->routeResult->shouldReceive('isMethodFailure')->once()->andReturn(false);
    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('returns 405 response with Allow header if route result is method failure', function () {
    $this->request->shouldReceive('getAttribute')->once()->with(RouteResultInterface::class)->andReturn($this->routeResult);
    $this->routeResult->shouldReceive('isMethodFailure')->once()->andReturn(true);
    $this->routeResult->shouldReceive('getAllowedMethods')->once()->andReturn(['GET', 'POST']);

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->getStatusCode())->toBe(405)
        ->and($response->getReasonPhrase())->toBe('Method Not Allowed')
        ->and($response->getHeaderLine('Allow'))->toBe('GET,POST');
});
