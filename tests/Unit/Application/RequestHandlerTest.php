<?php

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\MiddlewareInterface;
use Stein\Framework\Application\RequestHandler;

beforeEach(function () {
    $this->request_handler = new RequestHandler();
    $this->mock_request = Mockery::mock(ServerRequestInterface::class);
    $this->mock_middleware = Mockery::mock(MiddlewareInterface::class);
    $this->mock_response = Mockery::mock(ResponseInterface::class);
});

test('getTask method returns the correct task object', function () {
    expect($this->request_handler->getStack())->toBeInstanceOf(SplStack::class);
});

test('middleware method adds middleware to the stack', function () {
    $this->mock_middleware->shouldReceive('process')->andReturn($this->mock_response);
    $this->request_handler->middleware($this->mock_middleware);

    expect($this->request_handler->getStack())->toHaveCount(1);
});

test('handle method throws RuntimeException if stack is empty', function () {
    $this->request_handler->handle($this->mock_request);
})->throws(RuntimeException::class, 'The middleware stack is empty and no ResponseInterface has been returned...');

test('handle method processes request and returns response', function () {
    $this->mock_middleware->shouldReceive('process')->once()->with($this->mock_request, Mockery::any())->andReturn($this->mock_response);
    $this->request_handler->middleware($this->mock_middleware);

    $response = $this->request_handler->handle($this->mock_request);

    expect($response)->toBe($this->mock_response);
});
