<?php

namespace Stein\Framework\Router;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\Response\XmlResponse;
use Psr\Container\ContainerInterface;
use ReflectionMethod;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Attribute\{FromBody, FromQuery, Produces, Required};
use RuntimeException;

class RouteHandler implements RequestHandlerInterface
{

    public function __construct(
        protected array $handler,
        protected ContainerInterface $container
    ) {}

    public function getHandler(): array
    {
        return $this->handler;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $controller = $this->container->get($this->handler[0]);
        $method = $this->handler[1];
        $parameters = [];

        $reflection = new ReflectionMethod($controller, $method);
        foreach ($reflection->getParameters() as $parameter) {
            // If the route matched, all parameters are supposed to be in request attributes
            $name = $parameter->getName();
            $value = $request->getAttribute($name);
            $type = $parameter->getType()?->getName();
            $required = count($parameter->getAttributes(Required::class)) > 0;

            if ($value === null) {
                $default = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;

                if (count($parameter->getAttributes(FromQuery::class)) > 0) {
                    $value = $request->getQueryParams()[$name] ?? $default;
                } elseif (count($parameter->getAttributes(FromBody::class)) > 0) {
                    $value = $request->getParsedBody()[$name] ?? $default;
                } elseif ($type && $this->container->has($type)) {
                    $value = $this->container->get($type);
                } else {
                    $value = $default;
                }
            }

            if ($required && $value === null) {
                throw new \InvalidArgumentException('Missing required parameter: ' . $parameter->getName());
            }

            // Cast the parameter to the expected type (when applicable)
            $parameters[$name] = match ($type) {
                'array' => (array) $value,
                'bool' => (bool) $value,
                'float' => (float) $value,
                'int' => (int) $value,
                'string' => (string) $value,
                default => $value
            };
        }

        $response = call_user_func_array([$controller, $method], $parameters);
        if ($response instanceof ResponseInterface) {
            return $response;
        }

        $reflection = new ReflectionMethod($controller, $method);

        $attributes = $reflection->getAttributes(Produces::class);
        if (!count($attributes)) {
            throw new RuntimeException('The method did not return a ResponseInterface, and no content type attribute was provided.');
        }

        /** @var Produces $attribute */
        $attribute = $reflection->getAttributes(Produces::class)[0]?->newInstance();

        return match ($attribute->content_type) {
            'application/json' => new JsonResponse($response),
            'application/xml', 'text/xml' => new XmlResponse($response),
            'text/html' => new HtmlResponse($response),
            'text/plain' => new TextResponse($response),
            default => throw new RuntimeException('The method did not return a ResponseInterface, and no compatible content type was provided.')
        };
    }
}
