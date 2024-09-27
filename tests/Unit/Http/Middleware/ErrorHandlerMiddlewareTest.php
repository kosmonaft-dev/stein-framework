<?php

namespace Test\Unit\Http\Middleware;

use Mockery;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Stein\Framework\Http\Middleware\ErrorHandlerMiddleware;
use Stein\Framework\Http\Error\ApiCallException;

beforeEach(function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->handler = Mockery::mock(RequestHandlerInterface::class);
    $this->logger = Mockery::mock(LoggerInterface::class);
    $this->middleware = new ErrorHandlerMiddleware($this->logger);
});

test('processes request without error', function () {
    $response = Mockery::mock(ResponseInterface::class);

    $this->handler->shouldReceive('handle')->once()->with($this->request)->andReturn($response);

    $result = $this->middleware->process($this->request, $this->handler);

    expect($result)->toBe($response);
});

test('handles ApiCallException and returns ProblemDetails', function () {
    $exception = new ApiCallException('API error', 400);
    $exception->status_code = 400;
    $exception->reason_phrase = 'Bad Request';

    $this->handler->shouldReceive('handle')->once()->with($this->request)->andThrow($exception);
    $this->logger->shouldReceive('error')->once()->with($exception->getMessage(), Mockery::type('array'));
    $this->request->shouldReceive('getUri')->once()->andReturn(Mockery::mock(UriInterface::class, function ($mock) {
        $mock->shouldReceive('getPath')->once()->andReturn('/path');
    }));

    $result = $this->middleware->process($this->request, $this->handler);

    expect($result)->toBeInstanceOf(JsonResponse::class);
    $body = json_decode((string)$result->getBody(), true);
    expect($body['status'])->toBe(400)
        ->and($body['title'])->toBe('Bad Request')
        ->and($body['detail'])->toBe('API error');
});

test('handles generic exception and returns ProblemDetails', function () {
    $exception = new \Exception('Generic error');

    $this->handler->shouldReceive('handle')->once()->with($this->request)->andThrow($exception);
    $this->logger->shouldReceive('error')->once()->with($exception->getMessage(), Mockery::type('array'));
    $this->request->shouldReceive('getUri')->once()->andReturn(Mockery::mock(UriInterface::class, function ($mock) {
        $mock->shouldReceive('getPath')->once()->andReturn('/path');
    }));

    $result = $this->middleware->process($this->request, $this->handler);

    expect($result)->toBeInstanceOf(JsonResponse::class);
    $body = json_decode((string)$result->getBody(), true);
    expect($body['status'])->toBe(500)
        ->and($body['title'])->toBe('Internal Server Error')
        ->and($body['detail'])->toBe('Generic error');
});
