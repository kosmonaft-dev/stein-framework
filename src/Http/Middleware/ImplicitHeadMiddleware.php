<?php

namespace Stein\Framework\Http\Middleware;

use Laminas\Diactoros\Stream;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Stein\Framework\Router\RouteResultInterface;
use Stein\Framework\Router\RouterInterface;

class ImplicitHeadMiddleware implements MiddlewareInterface
{

    public function __construct(
        protected RouterInterface $router
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (strtoupper($request->getMethod()) != 'HEAD') {
            return $handler->handle($request);
        }

        $result = $request->getAttribute(RouteResultInterface::class);
        if (!$result) {
            return $handler->handle($request);
        }

        if ($result->getMatchedRoute()) {
            return $handler->handle($request);
        }

        $route_result = $this->router->match($request->withMethod('GET'));
        if ($route_result->isFailure()) {
            return $handler->handle($request);
        }

        $request = $request
            ->withAttribute(RouteResultInterface::class, $route_result)
            ->withMethod('GET');

        $response = $handler->handle($request);

        return $response->withBody(new Stream(''));
    }
}
