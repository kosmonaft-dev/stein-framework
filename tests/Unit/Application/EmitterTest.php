<?php

namespace P\Tests\Unit\Application;

use Mockery;
use Psr\Http\Message\{ResponseInterface, StreamInterface};
use Stein\Framework\Application\Emitter;

beforeEach(function () {
    // Start output buffering
    ob_start();
});

afterEach(function () {
    // Clean up output buffering
    ob_end_clean();
});

test('Emitter correctly emits the HTTP response', function () {
    $response = Mockery::mock(ResponseInterface::class);
    $stream = Mockery::mock(StreamInterface::class);

    // Configure the mock response
    $response->shouldReceive('getProtocolVersion')->andReturn('1.1');
    $response->shouldReceive('getStatusCode')->andReturn(200);
    $response->shouldReceive('getReasonPhrase')->andReturn('OK');
    $response->shouldReceive('getHeaders')->andReturn(['Content-Type' => ['application/json']]);
    $response->shouldReceive('getBody')->andReturn($stream);

    // Configure the mock stream
    $stream->shouldReceive('isSeekable')->andReturn(true);
    $stream->shouldReceive('rewind');
    $stream->shouldReceive('eof')->andReturn(false, true); // Simulate reading the stream twice
    $stream->shouldReceive('read')->andReturn('{"message": "Hello, world!"}');

    // Emit the response
    $emitter = new Emitter();
    $emitter->emit($response);

    // Assertions
    $output = ob_get_contents();
    expect($output)->toBe('{"message": "Hello, world!"}');
});
