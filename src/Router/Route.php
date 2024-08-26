<?php

namespace Stein\Framework\Router;

use JsonSerializable;
use function array_map, array_filter, is_string, strtoupper, sprintf, implode, in_array;

class Route implements RouteInterface, JsonSerializable
{

    public function __construct(
        /** @var string[] $methods */
        protected array $methods,
        protected string $path,
        /** @var array{0: string, 1: string} $handler */
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

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array{0: string, 1: string}
     */
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
