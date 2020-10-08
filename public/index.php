<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Database/ForumDatabase.php';

$forumDatabase = new ForumDatabase();

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, false);

$app->get('/forum_backend/api/v3/topics', function (Request $request, Response $response) use ($forumDatabase) {
    $json = $forumDatabase->getTopics();
    $response->getBody()->write($json);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->run();