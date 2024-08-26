<?php

namespace Stein\Framework\Application;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\MiddlewareInterface;
use RuntimeException;
use SplStack;

class RequestHandler implements RequestHandlerInterface
{

    public function __construct(
        /** @var SplStack<MiddlewareInterface> $stack */
        protected SplStack $stack = new SplStack()
    ) {}

    /**
     * @return SplStack<MiddlewareInterface>
     */
    public function getStack(): SplStack
    {
        return $this->stack;
    }

    public function middleware(MiddlewareInterface $middleware): static
    {
        $this->stack->push($middleware);

        return $this;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->stack->isEmpty()) {
            throw new RuntimeException('The middleware stack is empty and no ResponseInterface has been returned...');
        }

        return $this->stack->shift()->process($request, $this);
    }
}
