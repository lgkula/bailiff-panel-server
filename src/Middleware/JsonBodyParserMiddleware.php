<?php

declare(strict_types=1);

namespace BailiffPanel\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class JsonBodyParserMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (str_contains(strtolower($contentType), 'application/json')) {
            $body = trim((string) $request->getBody());
            $parsedBody = $body === '' ? null : json_decode($body, true);
            $request = $request->withParsedBody(is_array($parsedBody) ? $parsedBody : null);
        }

        return $handler->handle($request);
    }
}
