<?php

use Stein\Framework\Application\ControllerLoader;
use Stein\Framework\Router\RouterInterface;

beforeEach(function () {
    $this->router = Mockery::mock(RouterInterface::class);
    $this->directories = [dirname(__FILE__).'/../../../src/Application'];
    $this->cacheFile = tempnam(sys_get_temp_dir(), 'controllers.cache');
    $this->controllerLoader = new ControllerLoader($this->directories, $this->router, false, $this->cacheFile);
});

test('loads routes from cache if use_cache is true and cache file exists', function () {
    $this->controllerLoader = new ControllerLoader($this->directories, $this->router, true, $this->cacheFile);
    file_put_contents($this->cacheFile, json_encode([
        ['methods' => ['GET'], 'path' => '/test', 'handler' => ['TestController', 'test'], 'name' => 'test.route']
    ]));

    $this->router->shouldReceive('clear')->once();
    $this->router->shouldReceive('addRoute')->once();

    $this->controllerLoader->loadRoutes();

    unlink($this->cacheFile);
});

test('loads controllers and discovers routes if use_cache is false', function () {
    $this->router->shouldReceive('clear')->once();
    $this->router->shouldReceive('addRoute')->atLeast();
    $this->controllerLoader->loadRoutes();
});

afterEach(function () {
    Mockery::close();
});
