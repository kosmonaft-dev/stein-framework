<?php

namespace Stein\Framework\Http\Middleware;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use SimpleXMLElement;
use function strtolower, trim, explode, in_array, parse_str, json_decode, libxml_use_internal_errors, simplexml_load_string, libxml_clear_errors;

class BodyParserMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isNonBodyRequest($request)) {
            return $handler->handle($request);
        }

        $content_type = $this->getContentType($request);

        if ($this->isUrlEncoded($content_type)) {
            $request = $this->getUrlEncodedParsedBody($request);
        } elseif ($this->isJson($content_type)) {
            $request = $this->getJsonParsedBody($request);
        } elseif ($this->isXML($content_type)) {
            $request = $this->getXMLParsedBody($request);
        }

        return $handler->handle($request);
    }

    protected function isNonBodyRequest(ServerRequestInterface $request): bool
    {
        return in_array($request->getMethod(), [
            'GET',
            'HEAD',
            'OPTIONS'
        ]);
    }

    protected function getContentType(ServerRequestInterface $request): string
    {
        $content_type = strtolower(trim($request->getHeaderLine('Content-Type')));

        return explode(';', $content_type)[0];
    }

    protected function isUrlEncoded(string $content_type): bool
    {
        return $content_type == 'application/x-www-form-urlencoded';
    }

    protected function isJson(string $content_type): bool
    {
        return $content_type == 'application/json';
    }

    protected function isXML(string $content_type): bool
    {
        return in_array($content_type, [
            'application/xml',
            'text/xml'
        ]);
    }

    protected function getUrlEncodedParsedBody(ServerRequestInterface $request): ServerRequestInterface
    {
        $data = [];
        parse_str((string)$request->getBody(), $data);

        return $request->withParsedBody($data);
    }

    protected function getJsonParsedBody(ServerRequestInterface $request): ServerRequestInterface
    {
        $body = json_decode((string)$request->getBody(), true);
        if (is_array($body)) {
            $request = $request->withParsedBody($body);
        }

        return $request;
    }

    protected function getXMLParsedBody(ServerRequestInterface $request): ServerRequestInterface
    {
        $backup_errors = libxml_use_internal_errors(true);

        $xml = simplexml_load_string((string)$request->getBody());

        libxml_clear_errors();
        libxml_use_internal_errors($backup_errors);

        if ($xml instanceof SimpleXMLElement) {
            $request = $request->withParsedBody($xml);
        }

        return $request;
    }
}
