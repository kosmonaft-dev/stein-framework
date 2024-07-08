<?php

namespace Stein\Framework\Http\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Stein\Framework\Router\RouteHandler;
use Stein\Framework\Router\RouteResultInterface;
use Stein\Framework\Router\RouterInterface;

class RouteMiddleware implements MiddlewareInterface
{

    public function __construct(
        protected RouterInterface $router,
        protected ContainerInterface $container
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route_result = $this->router->match($request);

        $request = $request->withAttribute(RouteResultInterface::class, $route_result);
        if ($route_result->isSuccess()) {
            $route_handler = new RouteHandler($route_result->getMatchedRoute()->getHandler(), $this->container);
            $request = $request->withAttribute(RouteHandler::class, $route_handler);
        }

        return $handler->handle($request);
    }
}
