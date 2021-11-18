<?php

namespace App\Application\Middlewares;

use App\Infraestructure\Contexts\RequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        RequestContext::getInstance()->setUrl($request->getUri()->getPath());
        RequestContext::getInstance()->setBody(json_decode($request->getBody()->getContents()));
        RequestContext::getInstance()->setHeaders($request->getHeaders());
        RequestContext::getInstance()->setMethod($request->getMethod());
        return $handler->handle($request);
    }
}
