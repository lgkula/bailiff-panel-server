<?php

declare(strict_types=1);

namespace BailiffPanel\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response as SlimResponse;

final class CorsMiddleware implements MiddlewareInterface
{
    private const ALLOWED_ORIGINS = [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
    ];

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        if ($request->getMethod() === 'OPTIONS') {
            $response = new SlimResponse(204);
        } else {
            $response = $handler->handle($request);
        }

        $origin = $request->getHeaderLine('Origin');
        $allowedOrigin = in_array($origin, self::ALLOWED_ORIGINS, true) ? $origin : self::ALLOWED_ORIGINS[0];

        return $response
            ->withHeader('Access-Control-Allow-Origin', $allowedOrigin)
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
            ->withHeader('Access-Control-Max-Age', '86400');
    }
}
