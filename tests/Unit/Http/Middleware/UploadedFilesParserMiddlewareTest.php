<?php

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface, UploadedFileInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Stein\Framework\Http\Middleware\UploadedFilesParserMiddleware;

beforeEach(function () {
    $this->request = Mockery::mock(ServerRequestInterface::class);
    $this->handler = Mockery::mock(RequestHandlerInterface::class);
    $this->middleware = new UploadedFilesParserMiddleware();
});

test('processes request without uploaded files', function () {
    $this->request->shouldReceive('getUploadedFiles')->andReturn([]);
    $this->handler->shouldReceive('handle')->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('processes request with uploaded files', function () {
    $_FILES = [
        'file' => [
            'tmp_name' => tempnam(sys_get_temp_dir(), 'test'),
            'size' => 123,
            'error' => 0,
            'name' => 'test.txt',
            'type' => 'text/plain'
        ]
    ];

    $this->request->shouldReceive('withUploadedFiles')->andReturn($this->request);
    $this->handler->shouldReceive('handle')->with($this->request)->andReturn(Mockery::mock(ResponseInterface::class));

    $response = $this->middleware->process($this->request, $this->handler);

    expect($response)->toBeInstanceOf(ResponseInterface::class);
});

test('getUploadedFileLeaves processes single file', function () {
    $file = [
        'tmp_name' => tempnam(sys_get_temp_dir(), 'test'),
        'size' => 123,
        'error' => 0,
        'name' => 'test.txt',
        'type' => 'text/plain'
    ];

    $middleware = Mockery::mock(UploadedFilesParserMiddleware::class);
    $middleware->shouldAllowMockingProtectedMethods();
    $middleware->shouldReceive('getUploadedFileLeaves')->andReturn(Mockery::mock(UploadedFileInterface::class));

    $uploadedFile = $middleware->getUploadedFileLeaves($file);

    expect($uploadedFile)->toBeInstanceOf(UploadedFileInterface::class);
});

test('getUploadedFileLeaves processes multiple files', function () {
    $files = [
        'tmp_name' => [tempnam(sys_get_temp_dir(), 'test'), tempnam(sys_get_temp_dir(), 'test')],
        'size' => [123, 456],
        'error' => [0, 0],
        'name' => ['test1.txt', 'test2.txt'],
        'type' => ['text/plain', 'text/plain']
    ];

    $middleware = Mockery::mock(UploadedFilesParserMiddleware::class);
    $middleware->shouldAllowMockingProtectedMethods();
    $middleware->shouldReceive('getUploadedFileLeaves')->andReturn([
        Mockery::mock(UploadedFileInterface::class),
        Mockery::mock(UploadedFileInterface::class)
    ]);

    $uploadedFiles = $middleware->getUploadedFileLeaves($files);

    expect($uploadedFiles)->toBeArray()
        ->and($uploadedFiles[0])->toBeInstanceOf(UploadedFileInterface::class)
        ->and($uploadedFiles[1])->toBeInstanceOf(UploadedFileInterface::class);
});

afterEach(function () {
    Mockery::close();
});
