<?php

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Application\WebApplication;

test('WebApplication run method processes request and emits response', function () {
    // Mock the ServerRequestInterface
    $mock_request = Mockery::mock(ServerRequestInterface::class);

    // Mock the RequestHandlerInterface to return a response when handle is called
    $mock_handler = Mockery::mock(RequestHandlerInterface::class);
    $mock_response = Mockery::mock(ResponseInterface::class);
    $mock_handler->shouldReceive('handle')->once()->with($mock_request)->andReturn($mock_response);

    // Mock the Emitter within the WebApplication context to verify it emits the response
    // Assuming Emitter is a class you can mock or replace. If it's a final class or hard to replace,
    // you might need to refactor your code for better testability.
    $mock_rmitter = Mockery::mock('overload:Stein\Framework\Application\Emitter');
    $mock_rmitter->shouldReceive('emit')->once()->with($mock_response);

    // Instantiate WebApplication with the mocked RequestHandlerInterface
    $app = new WebApplication($mock_handler);

    // Execute the run method
    $app->run($mock_request);
});
