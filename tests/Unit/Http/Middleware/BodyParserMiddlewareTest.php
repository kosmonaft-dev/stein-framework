<?php

namespace P\Tests\Unit\Http\Middleware;

use Mockery;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface, StreamInterface};
use Psr\Http\Server\RequestHandlerInterface;
use SimpleXMLElement;
use Stein\Framework\Http\Middleware\BodyParserMiddleware;

beforeEach(function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->handler = Mockery::mock(RequestHandlerInterface::class);
    $this->middleware = new BodyParserMiddleware();
});

test('non-body requests bypass body parsing', function () {
    $this->request->shouldReceive('getMethod')->andReturn('GET');
    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('application/x-www-form-urlencoded content type is parsed', function () {
    $this->request->shouldReceive('getMethod')->andReturn('POST');
    $this->request->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/x-www-form-urlencoded');

    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('__toString')->andReturn('key=value&anotherKey=anotherValue');
    $this->request->shouldReceive('getBody')->andReturn($stream);

    $this->request->shouldReceive('withParsedBody')->once()->andReturnUsing(function ($body) {
        expect($body)->toBe(['key' => 'value', 'anotherKey' => 'anotherValue']);
        return $this->request;
    });

    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('application/json content type is parsed', function () {
    $this->request->shouldReceive('getMethod')->andReturn('POST');
    $this->request->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/json');

    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('__toString')->andReturn('{"key": "value", "anotherKey": "anotherValue"}');
    $this->request->shouldReceive('getBody')->andReturn($stream);

    $this->request->shouldReceive('withParsedBody')->once()->andReturnUsing(function ($body) {
        expect($body)->toBe(['key' => 'value', 'anotherKey' => 'anotherValue']);
        return $this->request;
    });

    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('application/xml content type is parsed', function () {
    $this->request->shouldReceive('getMethod')->andReturn('POST');
    $this->request->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/xml');

    $stream = Mockery::mock(StreamInterface::class);
    $stream->shouldReceive('__toString')->andReturn('<root><key>value</key><anotherKey>anotherValue</anotherKey></root>');
    $this->request->shouldReceive('getBody')->andReturn($stream);

    $this->request->shouldReceive('withParsedBody')->once()->andReturnUsing(function ($body) {
        expect($body)->toBeInstanceOf(SimpleXMLElement::class)
            ->and((string)$body->key)->toBe('value')
            ->and((string)$body->anotherKey)->toBe('anotherValue');
        return $this->request;
    });

    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});
