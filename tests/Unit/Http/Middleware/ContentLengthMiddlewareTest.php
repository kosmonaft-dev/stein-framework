<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Http\Middleware\ContentLengthMiddleware;

beforeEach(function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->handler = Mockery::mock(RequestHandlerInterface::class);
    $this->middleware = new ContentLengthMiddleware();
});

test('adds Content-Length header if not present', function () {
    $response = Mockery::mock(ResponseInterface::class);
    $stream = Mockery::mock(StreamInterface::class);

    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn($response);
    $response->shouldReceive('getBody')->andReturn($stream);
    $stream->shouldReceive('getSize')->andReturn(123);
    $response->shouldReceive('hasHeader')->with('Content-Length')->andReturn(false);
    $response->shouldReceive('withHeader')->with('Content-Length', '123')->andReturn($response);

    $result = $this->middleware->process($this->request, $this->handler);

    expect($result)->toBe($response);
});

test('does not add Content-Length header if already present', function () {
    $response = Mockery::mock(ResponseInterface::class);
    $stream = Mockery::mock(StreamInterface::class);

    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn($response);
    $response->shouldReceive('getBody')->andReturn($stream);
    $stream->shouldReceive('getSize')->andReturn(123);
    $response->shouldReceive('hasHeader')->with('Content-Length')->andReturn(true);

    $result = $this->middleware->process($this->request, $this->handler);

    expect($result)->toBe($response);
});

test('does not add Content-Length header if body size is null', function () {
    $response = Mockery::mock(ResponseInterface::class);
    $stream = Mockery::mock(StreamInterface::class);

    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn($response);
    $response->shouldReceive('getBody')->andReturn($stream);
    $stream->shouldReceive('getSize')->andReturn(null);
    $response->shouldReceive('hasHeader')->with('Content-Length')->andReturn(false);

    $result = $this->middleware->process($this->request, $this->handler);

    expect($result)->toBe($response);
});
