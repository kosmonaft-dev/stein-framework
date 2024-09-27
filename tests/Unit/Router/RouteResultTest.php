<?php

namespace P\Tests\Unit\Router;

use Mockery;
use Stein\Framework\Router\RouteResult;
use Stein\Framework\Router\RouteInterface;

beforeEach(function () {
    $this->route = Mockery::mock(RouteInterface::class);
});

test('creates a successful route result', function () {
    $params = ['id' => 1];
    $result = RouteResult::fromRouteSuccess($this->route, $params);

    expect($result->isSuccess())->toBeTrue()
        ->and($result->getMatchedRoute())->toBe($this->route)
        ->and($result->getMatchedParams())->toBe($params);
});

test('creates a failed route result', function () {
    $methods = ['GET', 'POST'];
    $result = RouteResult::fromRouteFailure($methods);

    expect($result->isSuccess())->toBeFalse()
        ->and($result->isFailure())->toBeTrue()
        ->and($result->isMethodFailure())->toBeTrue()
        ->and($result->getAllowedMethods())->toBe($methods);
});

test('gets matched route name', function () {
    $this->route->shouldReceive('getName')->andReturn('test.route');
    $result = RouteResult::fromRouteSuccess($this->route);

    expect($result->getMatchedRouteName())->toBe('test.route');
});

test('returns false for matched route name on failure', function () {
    $result = RouteResult::fromRouteFailure([]);

    expect($result->getMatchedRouteName())->toBeFalse();
});

test('returns false for matched route on failure', function () {
    $result = RouteResult::fromRouteFailure([]);

    expect($result->getMatchedRoute())->toBeFalse();
});

afterEach(function () {
    Mockery::close();
});
