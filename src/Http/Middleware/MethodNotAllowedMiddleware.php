<?php

namespace Stein\Framework\Http\Middleware;

use Laminas\Diactoros\Response;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Stein\Framework\Router\RouteResultInterface;

class MethodNotAllowedMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route_result = $request->getAttribute(RouteResultInterface::class);
        if (!$route_result || !$route_result->isMethodFailure()) {
            return $handler->handle($request);
        }

        $response = new Response();

        return $response
            ->withStatus(405, 'Method Not Allowed')
            ->withHeader(
                'Allow',
                implode(',', $route_result->getAllowedMethods())
            );
    }
}
