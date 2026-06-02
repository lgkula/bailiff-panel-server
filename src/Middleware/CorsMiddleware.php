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
    private const DEFAULT_ALLOWED_ORIGINS = [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'https://komornik.kula.wroclaw.pl',
    ];

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        if ($request->getMethod() === 'OPTIONS') {
            $response = new SlimResponse(204);
        } else {
            $response = $handler->handle($request);
        }

        $allowedOrigins = $this->allowedOrigins();
        $origin = $request->getHeaderLine('Origin');
        $allowedOrigin = in_array($origin, $allowedOrigins, true) ? $origin : $allowedOrigins[0];

        return $response
            ->withHeader('Access-Control-Allow-Origin', $allowedOrigin)
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
            ->withHeader('Access-Control-Max-Age', '86400');
    }

    private function allowedOrigins(): array
    {
        $configuredOrigins = getenv('ALLOWED_ORIGINS');

        if ($configuredOrigins === false || trim($configuredOrigins) === '') {
            return self::DEFAULT_ALLOWED_ORIGINS;
        }

        $origins = array_values(array_filter(
            array_map('trim', explode(',', $configuredOrigins)),
            static fn (string $origin): bool => $origin !== '',
        ));

        return $origins === [] ? self::DEFAULT_ALLOWED_ORIGINS : $origins;
    }
}
