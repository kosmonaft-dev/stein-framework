<?php

namespace P\Tests\Unit\Router;

use Mockery;
use P\Tests\Unit\Router\Asset\SampleController;
use Stein\Framework\Router\ControllerRouteMapper;
use Stein\Framework\Router\RouterInterface;
use Stein\Framework\Router\Route as RouterRoute;

beforeEach(function () {
    $this->router = Mockery::mock(RouterInterface::class);
    $this->cacheFile = tempnam(sys_get_temp_dir(), 'routes');
    $this->mapper = new ControllerRouteMapper($this->router, $this->cacheFile, true);
});

test('mapByClassString adds routes to the router', function () {
    $this->router->shouldReceive('clear')->once();
    $this->router->shouldReceive('addRoute')->times(1)->with(Mockery::type(RouterRoute::class));

    $controllers = [
        SampleController::class
    ];

    $this->mapper->mapByClassString($controllers);
});

test('mapByDirectory adds routes to the router', function () {
    $this->router->shouldReceive('clear')->once();
    $this->router->shouldReceive('addRoute')->times(1)->with(Mockery::type(RouterRoute::class));

    $directory = __DIR__ . '/Asset';

    $this->mapper->mapByDirectory($directory, '');
});

test('loadRoutesFromCache loads routes from cache file', function () {
    $routes = [
        [
            'methods' => ['GET'],
            'path' => '/sample',
            'handler' => ['App\\Controller\\SampleController', 'sampleMethod'],
            'name' => 'sample.route'
        ]
    ];

    file_put_contents($this->cacheFile, json_encode($routes));

    $this->router->shouldReceive('clear')->once();
    $this->router->shouldReceive('addRoute')->once()->with(Mockery::type(RouterRoute::class));

    $mapper = new ControllerRouteMapper($this->router, $this->cacheFile, false);
    $mapper->mapByClassString([]);
});