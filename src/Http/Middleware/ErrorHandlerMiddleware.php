<?php

namespace Stein\Framework\Http\Middleware;

use ErrorException;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Stein\Framework\Http\Error\ApiCallException;
use Stein\Framework\Http\Error\ProblemDetails;
use Throwable;

class ErrorHandlerMiddleware implements MiddlewareInterface
{

    public function __construct(
        protected LoggerInterface $logger
    ) {}

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $this->setErrorHandler();

            $response = $handler->handle($request);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode()
            ]);

            $problem_details = new ProblemDetails();
            $problem_details->detail = $e->getMessage();
            $problem_details->instance = $request->getUri()->getPath();
            $problem_details->extensions = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code'=> $e->getCode()
            ];

            if ($e instanceof ApiCallException) {
                $problem_details->status = $e->status_code;
                $problem_details->title = $e->reason_phrase;
            } else {
                $problem_details->status = 500;
                $problem_details->title = 'Internal Server Error';
            }

            $response = new JsonResponse($problem_details);
        }

        restore_error_handler();

        return $response;
    }

    protected function setErrorHandler(): void
    {
        set_error_handler(function(int $errno, string $errstr, string $errfile, int $errline): void {
            if (!(error_reporting() & $errno)) {
                // error_reporting does not include this error
                return;
            }

            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
    }
}
