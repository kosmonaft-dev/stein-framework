<?php

namespace Stein\Framework\Router;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Stein\Framework\Attribute\Controller;
use Stein\Framework\Attribute\HttpMethod;
use Stein\Framework\Attribute\RouteName;
use Stein\Framework\Attribute\Route;
use Stein\Framework\Router\Route as RouterRoute;
use Symfony\Component\Finder\Finder;

class ControllerRouteMapper
{

    protected array $controllers = [];
    protected array $routes = [];

    public function __construct(
        protected RouterInterface $router,
        protected ?string $cache_file = null,
        protected bool $cache_disabled = false
    ) {}

    public function mapByClassString(array $controllers): void
    {
        $this->controllers = $controllers;

        $this->map();
    }

    public function mapByDirectory(string $directory, string $namespace = 'Stein\\Api\\Controller\\'): void
    {
        $finder = Finder::create()
            ->files()
            ->in($directory)
            ->depth(0) // Stay in the same directory
            ->name('*Controller.php');

        $this->controllers = array_reduce(
            iterator_to_array($finder),
            fn($carry, $file) => array_merge($carry, [$namespace.$file->getBasename('.php')]),
            []
        );

        $this->map();
    }

    protected function map(): void
    {
        $this->router->clear();

        if ($this->cache_file && !$this->cache_disabled && file_exists($this->cache_file)) {
            $this->loadRoutesFromCache();
            return;
        }

        foreach ($this->controllers as $class) {
            $reflection = new ReflectionClass($class);
            if (!$this->isController($reflection)) {
                continue;
            }

            $routes = $reflection->getAttributes(Route::class);
            if (count($routes) === 0) {
                continue;
            }

            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach ($routes as $route) {
                $this->processRoute($route->newInstance(), $methods, $reflection);
            }
        }

        if ($this->cache_file && !$this->cache_disabled) {
            file_put_contents($this->cache_file, json_encode($this->routes));
        }
    }

    protected function isController(ReflectionClass $reflection): bool
    {
        return count($reflection->getAttributes(Controller::class, ReflectionAttribute::IS_INSTANCEOF)) > 0;
    }

    protected function processRoute(Route $route, array $methods, ReflectionClass $reflection): void
    {
        foreach ($methods as $method) {
            $httpMethods = $method->getAttributes(HttpMethod::class, ReflectionAttribute::IS_INSTANCEOF);
            $name = $method->getAttributes(RouteName::class)[0] ?? null;

            foreach ($httpMethods as $httpMethod) {
                $this->addRouteFromMethod($route, $httpMethod->newInstance(), $method, $reflection, $name);
            }
        }
    }

    protected function addRouteFromMethod(Route $route, HttpMethod $httpMethod, ReflectionMethod $method, ReflectionClass $reflection, ?ReflectionAttribute $name): void
    {
        $this->addRoute(new RouterRoute(
            $httpMethod->methods,
            '/' . trim($route->path . '/' . $httpMethod->path, '/ '),
            [$reflection->getName(), $method->getName()],
            $name?->newInstance()->name
        ));
    }

    protected function addRoute(RouterRoute $route): void
    {
        $this->routes[] = $route;
        $this->router->addRoute($route);
    }

    protected function loadRoutesFromCache(): void
    {
        $this->routes = json_decode(file_get_contents($this->cache_file), true);
        foreach ($this->routes as $route) {
            $this->router->addRoute(new RouterRoute(
                $route['methods'],
                $route['path'],
                $route['handler'],
                $route['name']
            ));
        }
    }
}
