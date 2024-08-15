<?php

namespace Stein\Framework\Router;

use JsonSerializable;

class Route implements RouteInterface, JsonSerializable
{

    public function __construct(
        protected array $methods,
        protected string $path,
        protected array $handler,
        protected ?string $name = null
    ) {
        $this->methods = array_map('strtoupper', array_filter($methods, 'is_string'));
        $this->name = $name ?: sprintf(
            '%s^%s',
            implode(':', $this->methods),
            $this->path
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'methods' => $this->methods,
            'path' => $this->path,
            'handler' => $this->handler,
            'name' => $this->name
        ];
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHandler(): array
    {
        return $this->handler;
    }

    public function getAllowedMethods(): array
    {
        return $this->methods;
    }

    public function allowsMethod(string $method): bool
    {
        return in_array(strtoupper($method), $this->methods);
    }
}
