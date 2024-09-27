<?php

namespace Test\Unit\Router;

use InvalidArgumentException;
use Mockery;
use Psr\Http\Message\ServerRequestInterface;
use Stein\Framework\Router\{FastRouteRouter, RouteInterface, RouteResultInterface};

beforeEach(function () {
    $this->router = new FastRouteRouter();
    $this->route = Mockery::mock(RouteInterface::class);
    $this->request = Mockery::mock(ServerRequestInterface::class);
});

test('adds a route', function () {
    $this->route->shouldReceive('getName')->andReturn('test.route');
    $this->router->addRoute($this->route);

    expect($this->router->getRoutes())->toHaveKey('test.route');
});

test('throws exception when adding a route with duplicate name', function () {
    $this->route->shouldReceive('getName')->andReturn('test.route');
    $this->router->addRoute($this->route);

    $this->route->shouldReceive('getName')->andReturn('test.route');
    expect(fn() => $this->router->addRoute($this->route))->toThrow(InvalidArgumentException::class);
});

test('clears all routes', function () {
    $this->route->shouldReceive('getName')->andReturn('test.route');
    $this->router->addRoute($this->route);
    $this->router->clear();

    expect($this->router->getRoutes())->toBeEmpty();
});

test('matches a request to a route', function () {
    $this->route->shouldReceive('getName')->andReturn('test.route');
    $this->route->shouldReceive('getAllowedMethods')->andReturn(['GET']);
    $this->route->shouldReceive('getPath')->andReturn('/test');
    $this->router->addRoute($this->route);

    $this->request->shouldReceive('getMethod')->andReturn('GET');
    $this->request->shouldReceive('getUri->getPath')->andReturn('/test');

    $result = $this->router->match($this->request);

    expect($result)->toBeInstanceOf(RouteResultInterface::class);
    expect($result->isSuccess())->toBeTrue();
});

test('generates a URI for a named route', function () {
    $this->route->shouldReceive('getName')->andReturn('test.route');
    $this->route->shouldReceive('getPath')->andReturn('/test/{id}');
    $this->router->addRoute($this->route);

    $uri = $this->router->generateUri('test.route', ['id' => 123]);

    expect($uri)->toBe('/test/123');
});

afterEach(function () {
    Mockery::close();
});
