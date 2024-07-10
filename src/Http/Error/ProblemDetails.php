<?php

namespace Stein\Framework\Http\Error;

class ProblemDetails implements \JsonSerializable
{

    public string $type;
    public string $title;
    public int $status;
    public string $detail;
    public string $instance;

    protected function getRFCSection(int $status_code): string
    {
        $uri = 'https://datatracker.ietf.org/doc/html/rfc7231#section-6';

        $uri .= match ($status_code) {
            400 => '.5.1',
            402 => '.5.2',
            403 => '.5.3',
            404 => '.5.4',
            405 => '.5.5',
            406 => '.5.6',
            408 => '.5.7',
            409 => '.5.8',
            410 => '.5.9',
            411 => '.5.10',
            413 => '.5.11',
            414 => '.5.12',
            415 => '.5.13',
            417 => '.5.14',
            426 => '.5.15',
            500 => '.6.1',
            501 => '.6.2',
            502 => '.6.3',
            503 => '.6.4',
            504 => '.6.5',
            505 => '.6.6',
            default => ''
        };

        return $uri;
    }

    protected function getReasonPhrase(int $status_code): string
    {
        return match ($status_code) {
            // INFORMATIONAL CODES
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            103 => 'Early Hints',
            // SUCCESS CODES
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            // REDIRECTION CODES
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy', // Deprecated to 306 => '(Unused)'
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            // CLIENT ERROR
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Content Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            421 => 'Misdirected Request',
            422 => 'Unprocessable Content',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Too Early',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            444 => 'Connection Closed Without Response',
            451 => 'Unavailable For Legal Reasons',
            // SERVER ERROR
            499 => 'Client Closed Request',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended (OBSOLETED)',
            511 => 'Network Authentication Required',
            599 => 'Network Connect Timeout Error',
            default => 'Internal Server Error'
        };
    }

    public function jsonSerialize(): mixed
    {
        return array_filter([
            'type' => $this->getRFCSection($this->status),
            'title' => $this->title,
            'status' => $this->status ?: $this->getReasonPhrase($this->status),
            'detail' => $this->detail,
            'instance' => $this->instance
        ]);
    }
}
