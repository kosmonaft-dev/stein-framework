<?php

namespace Stein\Framework\Application;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use RegexIterator;
use Stein\Framework\Attribute\Controller;
use Stein\Framework\Attribute\HttpMethod;
use Stein\Framework\Attribute\RouteName;
use Stein\Framework\Attribute\Route;
use Stein\Framework\Router\Route as RouterRoute;
use Stein\Framework\Router\RouterInterface;

class ControllerLoader
{

    public function __construct(
        protected array $directories,
        protected RouterInterface $router
    ) {}

    public function loadRoutes()
    {
        $this->loadControllers();
        $this->discoverControllers();
    }

    protected function loadControllers(): void
    {
        foreach ($this->directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $directory_iterator = new RecursiveDirectoryIterator($directory);
            $iterator = new RecursiveIteratorIterator($directory_iterator);
            $files = new RegexIterator($iterator, '/^.+\.php$/i', RegexIterator::GET_MATCH);

            foreach ($files as $phpFile) {
                require_once $phpFile[0];
            }
        }
    }

    protected function discoverControllers(): void
    {
        $this->router->clear();

        foreach (get_declared_classes() as $class) {
            $reflection = new ReflectionClass($class);
            if (!count($reflection->getAttributes(Controller::class, ReflectionAttribute::IS_INSTANCEOF))) {
                continue;
            }

            $routes = $reflection->getAttributes(Route::class);
            if (count($routes) === 0) {
                continue;
            }

            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach ($routes as $route) {
                $route = $route->newInstance();
                /** @var Route $route */

                foreach ($methods as $method) {
                    $http_methods = $method->getAttributes(HttpMethod::class, ReflectionAttribute::IS_INSTANCEOF);
                    $name = $method->getAttributes(RouteName::class)[0] ?? null;

                    foreach ($http_methods as $http_method) {
                        $http_method = $http_method->newInstance();

                        /** @var HttpMethod $http_method */
                        $this->router->addRoute(new RouterRoute(
                            $http_method->methods,
                            '/'.trim($route->path.'/'.$http_method->path, '/ '),
                            [$reflection->getName(), $method->getName()],
                            $name?->newInstance()->name
                        ));
                    }
                }
            }
        }
    }
}