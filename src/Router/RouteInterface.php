<?php

namespace Stein\Framework\Router;

use Psr\Http\Server\RequestHandlerInterface;

interface RouteInterface
{

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return array
     */
    public function getHandler(): array;

    /**
     * @return array
     */
    public function getAllowedMethods(): array;

    /**
     * @param string $method
     * @return bool
     */
    public function allowsMethod(string $method): bool;
}
