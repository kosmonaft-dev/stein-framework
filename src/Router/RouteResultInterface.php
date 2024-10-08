<?php

namespace Stein\Framework\Router;

interface RouteResultInterface
{

    /**
     * @param RouteInterface $route
     * @param array<string, string> $params
     * @return RouteResultInterface
     */
    public static function fromRouteSuccess(RouteInterface $route, array $params = []): RouteResultInterface;

    /**
     * @param string[] $methods
     * @return RouteResultInterface
     */
    public static function fromRouteFailure(array $methods): RouteResultInterface;

    /**
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * @return false|RouteInterface
     */
    public function getMatchedRoute();

    /**
     * @return false|string
     */
    public function getMatchedRouteName();

    /**
     * @return array<string, string>
     */
    public function getMatchedParams(): array;

    /**
     * @return bool
     */
    public function isFailure(): bool;

    /**
     * @return bool
     */
    public function isMethodFailure(): bool;

    /**
     * @return string[]
     */
    public function getAllowedMethods(): array;
}
