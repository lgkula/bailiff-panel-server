<?php

declare(strict_types=1);

namespace BailiffPanel\Controllers;

use BailiffPanel\Services\BailiffRepository;
use BailiffPanel\Services\BailiffValidator;
use BailiffPanel\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class BailiffController
{
    public function __construct(
        private BailiffRepository $repository,
        private BailiffValidator $validator,
        private ResponseFactory $responses,
    ) {
    }

    public function index(Request $request, Response $response): Response
    {
        $query = $request->getQueryParams();
        $search = isset($query['search']) && trim((string) $query['search']) !== ''
            ? trim((string) $query['search'])
            : null;
        $bailiffs = $this->repository->findAll($search);

        return $this->responses->json($response, [
            'data' => $bailiffs,
            'meta' => [
                'count' => count($bailiffs),
                'search' => $search,
            ],
        ]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $bailiff = $this->repository->findById((string) $args['id']);

        if ($bailiff === null) {
            return $this->notFound($response);
        }

        return $this->responses->json($response, ['data' => $bailiff]);
    }

    public function create(Request $request, Response $response): Response
    {
        $payload = $this->payload($request);

        if ($payload === null) {
            return $this->invalidJson($response);
        }

        $errors = $this->validator->validate($payload);
        if ($errors !== []) {
            return $this->validationError($response, $errors);
        }

        $bailiff = $this->repository->create($this->validator->sanitize($payload));

        return $this->responses->json($response, ['data' => $bailiff], 201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        if ($this->repository->findById((string) $args['id']) === null) {
            return $this->notFound($response);
        }

        $payload = $this->payload($request);

        if ($payload === null) {
            return $this->invalidJson($response);
        }

        $errors = $this->validator->validate($payload);
        if ($errors !== []) {
            return $this->validationError($response, $errors);
        }

        $bailiff = $this->repository->update((string) $args['id'], $this->validator->sanitize($payload));

        return $this->responses->json($response, ['data' => $bailiff]);
    }

    private function payload(Request $request): ?array
    {
        $payload = $request->getParsedBody();

        return is_array($payload) ? $payload : null;
    }

    private function invalidJson(Response $response): Response
    {
        return $this->responses->error($response, 400, 'INVALID_JSON', 'Request body must be a valid JSON object.');
    }

    private function notFound(Response $response): Response
    {
        return $this->responses->error($response, 404, 'BAILIFF_NOT_FOUND', 'Bailiff with given id was not found.');
    }

    private function validationError(Response $response, array $errors): Response
    {
        return $this->responses->error(
            $response,
            422,
            'VALIDATION_ERROR',
            'Request body contains invalid fields.',
            $errors,
        );
    }
}
