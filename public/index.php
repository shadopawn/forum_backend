<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, false);

$app->get('/forum_backend/api/v3/topics', function (Request $request, Response $response) {
    $data = array(
        'data' => array(
            array(
                'id' => '1',
                'name' => 'programming'
            ),
            array(
                'id' => '2',
                'name' => 'design'
            )
        )
    );
    $payload = json_encode($data);

    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->run();