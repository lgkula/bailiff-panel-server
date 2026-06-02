<?php

declare(strict_types=1);

namespace BailiffPanel\Services;

use Psr\Http\Message\ResponseInterface as Response;

final class ResponseFactory
{
    public function json(Response $response, array $payload, int $status = 200): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($json === false ? '{}' : $json);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    public function error(Response $response, int $status, string $code, string $message, ?array $fields = null): Response
    {
        $payload = [
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];

        if ($fields !== null) {
            $payload['error']['fields'] = $fields;
        }

        return $this->json($response, $payload, $status);
    }
}
