<?php

namespace Stein\Framework\Router;

use FastRoute\{
    Dispatcher,
    RouteCollector,
    RouteParser\Std
};
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use function FastRoute\{
    cachedDispatcher,
    simpleDispatcher
};

class FastRouteRouter implements RouterInterface
{

    public function __construct(
        /** @var RouteInterface[]  */
        protected array $routes = [],
        protected ?string $cache_file = null,
        protected bool $cache_disabled = false
    ) {}

    public function getCacheFile(): ?string
    {
        return $this->cache_file;
    }

    public function setCacheFile(string $cache_file): void
    {
        $this->cache_file = $cache_file;
    }

    public function isCacheDisabled(): bool
    {
        return $this->cache_disabled;
    }

    public function setCacheDisabled(bool $cache_disabled): void
    {
        $this->cache_disabled = $cache_disabled;
    }

    protected function getDispatcher(callable $callable): Dispatcher
    {
        if (is_string($this->cache_file)) {
            return cachedDispatcher($callable, [
                'cacheFile' => $this->cache_file,
                'cacheDisabled' => $this->cache_disabled
            ]);
        }

        return simpleDispatcher($callable);
    }

    public function addRoute(RouteInterface $route): void
    {
        if (isset($this->routes[$route->getName()])) {
            throw new InvalidArgumentException(sprintf(
                'A similar route name (%s) has already been provided.',
                $route->getName()
            ));
        }

        $this->routes[$route->getName()] = $route;
    }

    public function clear(): void
    {
        $this->routes = [];
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function match(ServerRequestInterface $request): RouteResultInterface
    {
        $dispatcher = $this->getDispatcher(function(RouteCollector $collector) {
            foreach ($this->routes as $route) {
                $collector->addRoute($route->getAllowedMethods(), $route->getPath(), $route->getName());
            }
        });

        $route_info = $dispatcher->dispatch(
            $request->getMethod(),
            rawurldecode($request->getUri()->getPath())
        );

        if ($route_info[0] == Dispatcher::FOUND) {
            return RouteResult::fromRouteSuccess($this->routes[$route_info[1]], $route_info[2]);
        }

        return RouteResult::fromRouteFailure(
            $route_info[0] == Dispatcher::METHOD_NOT_ALLOWED ?
                $route_info[1] : []
        );
    }

    public function generateUri(string $name, array $substitutions = []): string
    {
        if (!isset($this->routes[$name])) {
            throw new InvalidArgumentException(sprintf(
                'The route named %s is unknown...',
                $name
            ));
        }

        // Reverse the array so we start by the longest possible URI.
        $routes = array_reverse((new Std())->parse($this->routes[$name]->getPath()));

        foreach ($routes as $parts) {
            if (!$this->routeCanBeGenerated($parts, $substitutions)) {
                continue;
            }

            return $this->buildUri($parts, $substitutions);
        }

        throw new RuntimeException(sprintf(
            'Unable to generate URI "%s", missing substitutions, only received : %s...',
            $this->routes[$name]->getPath(),
            implode(', ', $substitutions)
        ));
    }

    protected function routeCanBeGenerated(array $parts, array $substitutions): bool
    {
        foreach ($parts as $part) {
            if (is_string($part)) {
                continue;
            }

            if (!isset($substitutions[$part[0]])) {
                return false;
            }
        }

        return true;
    }

    protected function buildUri(array $parts, array $substitutions): string
    {
        $uri = '';
        foreach ($parts as $part) {
            if (is_string($part)) {
                $uri .= $part;
                continue;
            }

            if (!preg_match('#^'.$part[1].'$#', (string)$substitutions[$part[0]])) {
                throw new RuntimeException(sprintf(
                    'Given substitution for "%s" (= %s) does not match the route constraint "%s"...',
                    $part[0],
                    (string)$substitutions[$part[0]],
                    $part[1]
                ));
            }

            $uri .= $substitutions[$part[0]];
        }

        return $uri;
    }
}
