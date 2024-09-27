<?php

namespace P\Tests\Unit\Http\Middleware;

use Mockery;
use Laminas\Diactoros\Uri;
use Laminas\Diactoros\Response\RedirectResponse;
use Mockery\MockInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Http\Middleware\TrailingSlashMiddleware;

beforeEach(function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->handler = Mockery::mock(RequestHandlerInterface::class);
    $this->middleware = new TrailingSlashMiddleware();
});

test('redirects to non-trailing slash URL for GET requests', function () {
    $uri = (new Uri())->withPath('/test/');
    $this->request->shouldReceive('getUri')->andReturn($uri);
    $this->request->shouldReceive('getMethod')->andReturn('GET');

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response)->toBeInstanceOf(ResponseInterface::class);
});

test('removes trailing slash for non-GET requests', function () {
    $uri = (new Uri())->withPath('/test/');
    $this->request->shouldReceive('getUri')->andReturn($uri);
    $this->request->shouldReceive('getMethod')->andReturn('POST');
    $this->request->shouldReceive('withUri')->andReturnUsing(function ($uri) {
        return $this->request;
    });

    $this->handler->shouldReceive('handle')->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    /** @var MockInterface $response */
    $response = $this->middleware->process($this->request, $this->handler);
    $response->shouldReceive('getStatusCode')->andReturn(301);
    $response->shouldReceive('getHeaderLine')->andReturn('/test');

    expect($response->getStatusCode())->toBe(301)
        ->and($response->getHeaderLine('Location'))->toBe('/test');
});

test('does not modify request without trailing slash', function () {
    $uri = (new Uri())->withPath('/test');
    $this->request->shouldReceive('getUri')->andReturn($uri);

    $this->handler->shouldReceive('handle')->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

afterEach(function () {
    Mockery::close();
});
