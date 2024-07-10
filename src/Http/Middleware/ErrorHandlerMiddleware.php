<?php

namespace Stein\Framework\Http\Middleware;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Stein\Framework\Http\Error\ApiCallException;
use Stein\Framework\Http\Error\ProblemDetails;
use Throwable;

class ErrorHandlerMiddleware implements MiddlewareInterface
{

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ApiCallException $e) {
            $problem_details = new ProblemDetails();
            $problem_details->status = $e->status_code;
            $problem_details->title = $e->reason_phrase;
            $problem_details->detail = $e->getMessage();
            $problem_details->instance = $request->getUri()->getPath();

            return new JsonResponse($problem_details);
        } catch (Throwable $e) {
            $problem_details = new ProblemDetails();
            $problem_details->status = 500;
            $problem_details->title = 'Internal Server Error';
            $problem_details->detail = $e->getMessage();
            $problem_details->instance = $request->getUri()->getPath();

            return new JsonResponse($problem_details);
        }
    }
}