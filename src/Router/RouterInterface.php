<?php

namespace Stein\Framework\Router;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    /**
     * @param RouteInterface $route
     * @return void
     * @throws InvalidArgumentException If a route with same name already exists.
     */
    public function addRoute(RouteInterface $route): void;

    /**
     * @return void
     */
    public function clear(): void;

    /**
     * @return RouteInterface[]
     */
    public function getRoutes(): array;

    /**
     * @param ServerRequestInterface $request
     * @return RouteResultInterface
     */
    public function match(ServerRequestInterface $request): RouteResultInterface;

    /**
     * @param string $name
     * @param array $substitutions
     * @return string
     * @throws InvalidArgumentException If the route name is unknown.
     */
    public function generateUri(string $name, array $substitutions = []): string;
}
