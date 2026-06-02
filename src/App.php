<?php

declare(strict_types=1);

namespace BailiffPanel;

use BailiffPanel\Controllers\BailiffController;
use BailiffPanel\Controllers\RandomErrorController;
use BailiffPanel\Middleware\CorsMiddleware;
use BailiffPanel\Middleware\JsonBodyParserMiddleware;
use BailiffPanel\Services\BailiffRepository;
use BailiffPanel\Services\BailiffValidator;
use BailiffPanel\Services\ResponseFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\App as SlimApp;

final class App
{
    public static function create(string $rootPath): SlimApp
    {
        $app = AppFactory::create();
        $app->addRoutingMiddleware();
        $app->add(new JsonBodyParserMiddleware());
        $app->add(new CorsMiddleware());

        $responses = new ResponseFactory();
        $repository = new BailiffRepository(
            $rootPath . '/data/bailiffs.json',
            $rootPath . '/data/bailiffs.seed.json',
        );
        $validator = new BailiffValidator();

        $bailiffs = new BailiffController($repository, $validator, $responses);
        $randomError = new RandomErrorController($responses);

        $app->get('/api/bailiffs', [$bailiffs, 'index']);
        $app->get('/api/bailiffs/{id}', [$bailiffs, 'show']);
        $app->post('/api/bailiffs', [$bailiffs, 'create']);
        $app->put('/api/bailiffs/{id}', [$bailiffs, 'update']);
        $app->get('/api/random-error', [$randomError, 'show']);
        $app->get('/docs/openapi.yaml', static function (Request $request, Response $response) use ($rootPath): Response {
            $openApi = file_get_contents($rootPath . '/docs/openapi.yaml');
            $response->getBody()->write($openApi === false ? '' : $openApi);

            return $response->withHeader('Content-Type', 'application/yaml');
        });

        $app->map(['GET'], '/', static function (Request $request, Response $response): Response {
            return (new ResponseFactory())->json($response, [
                'data' => [
                    'name' => 'Bailiff Panel API',
                    'docs' => '/docs/openapi.yaml',
                ],
            ]);
        });

        $errorMiddleware = $app->addErrorMiddleware(true, true, true);
        $errorMiddleware->setDefaultErrorHandler(
            static function (Request $request, \Throwable $exception, bool $displayErrorDetails) use ($app, $responses): Response {
                $response = $app->getResponseFactory()->createResponse();

                return $responses->error(
                    $response,
                    500,
                    'INTERNAL_SERVER_ERROR',
                    $displayErrorDetails ? $exception->getMessage() : 'Unexpected server error.',
                );
            },
        );

        return $app;
    }
}
