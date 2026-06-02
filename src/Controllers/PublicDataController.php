<?php

declare(strict_types=1);

namespace BailiffPanel\Controllers;

use BailiffPanel\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class PublicDataController
{
    private const STATUSES = [205, 400, 401, 403, 404, 429, 500, 502, 503];

    public function __construct(private ResponseFactory $responses)
    {
    }

    public function pull(Request $request, Response $response): Response
    {
        $status = self::STATUSES[array_rand(self::STATUSES)];
        $message = $status === 205
            ? 'Próba pobierania danych zakończyła sie nieoczekiwanym wynikiem, ponów próbę.'
            : 'Próba pobierania danych zakończyła sie błędem.';

        return $this->responses
            ->json($response, ['message' => $message], $status)
            ->withHeader('X-Public-Data-Message', rawurlencode($message));
    }
}
