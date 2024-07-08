<?php

namespace Stein\Framework\Http\Middleware;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Stein\Framework\Router\{RouteHandler, RouteResultInterface};

class DispatchMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route_handler = $request->getAttribute(RouteHandler::class);
        $route_result = $request->getAttribute(RouteResultInterface::class);

        if (!$route_result || !$route_handler) {
            return $handler->handle($request);
        }

        foreach ($route_result->getMatchedParams() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $route_handler->handle($request);
    }
}
