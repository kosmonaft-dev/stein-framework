<?php

namespace Test\Unit\Router;

use Stein\Framework\Router\Route;

beforeEach(function () {
    $this->route = new Route(['GET'], '/test', ['TestController', 'test'], 'test.route');
});

test('gets the route name', function () {
    expect($this->route->getName())->toBe('test.route');
});

test('gets the route path', function () {
    expect($this->route->getPath())->toBe('/test');
});

test('gets the allowed methods', function () {
    expect($this->route->getAllowedMethods())->toBe(['GET']);
});

test('gets the handler', function () {
    expect($this->route->getHandler())->toBe(['TestController', 'test']);
});
