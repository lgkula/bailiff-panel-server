<?php

declare(strict_types=1);

namespace BailiffPanel\Controllers;

use BailiffPanel\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class RandomErrorController
{
    public function __construct(private ResponseFactory $responses)
    {
    }

    public function show(Request $request, Response $response): Response
    {
        $roll = random_int(1, 100);

        if ($roll <= 50) {
            return $this->responses->json($response, [
                'data' => [
                    'status' => 'ok',
                    'message' => 'Random endpoint returned success.',
                ],
            ]);
        }

        if ($roll <= 70) {
            return $this->responses->error($response, 400, 'RANDOM_BAD_REQUEST', 'Random endpoint returned a bad request intentionally.');
        }

        if ($roll <= 90) {
            return $this->responses->error($response, 500, 'RANDOM_FAILURE', 'Random endpoint failed intentionally.');
        }

        sleep(random_int(3, 5));

        if (random_int(0, 1) === 1) {
            return $this->responses->json($response, [
                'data' => [
                    'status' => 'ok',
                    'message' => 'Random endpoint returned success after a delay.',
                ],
            ]);
        }

        return $this->responses->error($response, 504, 'RANDOM_TIMEOUT', 'Random endpoint simulated a timeout-like delay.');
    }
}
