<?php

namespace Stein\Framework\Router;

use Psr\Container\ContainerInterface;
use function count, is_array;

final class RouteResult implements RouteResultInterface
{

    protected RouteInterface $route;
    /** @var array<string, string> $params */
    protected array $params = [];
    protected bool $success;
    /** @var string[]|null $methods */
    protected ?array $methods = null;
    protected ContainerInterface $container;

    protected function __construct() {}

    /**
     * @param RouteInterface $route
     * @param array<string, string> $params
     * @return RouteResultInterface
     */
    public static function fromRouteSuccess(RouteInterface $route, array $params = []): RouteResultInterface
    {
        $result = new static();
        $result->success = true;
        $result->route = $route;
        $result->params = $params;

        return $result;
    }

    /**
     * @param string[] $methods
     * @return RouteResultInterface
     */
    public static function fromRouteFailure(array $methods): RouteResultInterface
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

    /**
     * @return array<string, string>
     */
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
