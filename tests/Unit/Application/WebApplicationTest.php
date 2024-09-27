<?php

namespace Test\Unit\Application;

use Laminas\Diactoros\Stream;
use Mockery;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Application\Emitter;
use Stein\Framework\Application\WebApplication;

test('WebApplication run method processes request and emits response', function () {
    // Mock the ServerRequestInterface
    $mock_request = Mockery::mock(ServerRequestInterface::class);

    // Mock the RequestHandlerInterface to return a response when handle is called
    $mock_handler = Mockery::mock(RequestHandlerInterface::class);
    $mock_response = Mockery::mock(ResponseInterface::class);
    $mock_stream = Mockery::mock(Stream::class);

    $mock_stream->shouldReceive('isSeekable')->once()->andReturn(true);
    $mock_stream->shouldReceive('rewind')->once();
    $mock_stream->shouldReceive('eof')->once()->andReturn(true);

    $mock_response->shouldReceive('getStatusCode')->twice()->andReturn(200);
    $mock_response->shouldReceive('getReasonPhrase')->once()->andReturn('OK');
    $mock_response->shouldReceive('getHeaders')->once()->andReturn([]);
    $mock_response->shouldReceive('getBody')->once()->andReturn($mock_stream);
    $mock_response->shouldReceive('getProtocolVersion')->once()->andReturn('1.1');

    $mock_handler->shouldReceive('handle')->once()->with($mock_request)->andReturn($mock_response);

    // Instantiate WebApplication with the mocked RequestHandlerInterface
    $app = new WebApplication($mock_handler);

    // Execute the run method
    $app->run($mock_request);
});
