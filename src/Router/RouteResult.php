<?php

namespace Stein\Framework\Router;

use Psr\Container\ContainerInterface;

class RouteResult implements RouteResultInterface
{

    protected RouteInterface $route;
    protected array $params = [];
    protected bool $success;
    protected ?array $methods = null;
    protected ContainerInterface $container;

    protected function __construct() {}

    final public static function fromRouteSuccess(RouteInterface $route, array $params = []): RouteResultInterface
    {
        $result = new static();
        $result->success = true;
        $result->route = $route;
        $result->params = $params;

        return $result;
    }

    final public static function fromRouteFailure(array $methods): RouteResultInterface
    {
        $result = new static();
        $result->success = false;
        $result->methods = $methods;

        return $result;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMatchedRoute(): RouteInterface|false
    {
        if ($this->success) {
            return $this->route;
        }

        return false;
    }

    public function getMatchedRouteName(): string|false
    {
        if ($this->success) {
            return $this->route->getName();
        }

        return false;
    }

    public function getMatchedParams(): array
    {
        return $this->params;
    }

    public function isFailure(): bool
    {
        return !$this->success;
    }

    public function isMethodFailure(): bool
    {
        return !$this->success && is_array($this->methods) && count($this->methods);
    }

    public function getAllowedMethods(): array
    {
        return $this->methods ?: [];
    }
}
