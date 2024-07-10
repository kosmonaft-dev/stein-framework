<?php

namespace Stein\Framework\Http\Middleware;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

class ErrorHandlerMiddleware implements MiddlewareInterface
{

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 500,
                'title' => 'Internal Server Error',
                'detail' => $e->getMessage(),
                'instance' => $request->getUri()->getPath()
            ], 500);
        }
    }
}