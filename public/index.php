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
    $jsonResponse = $forumDatabase->getTopics();
    $response->getBody()->write($jsonResponse);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/forum_backend/api/v3/topics/{topicID}/threads', function (Request $request, Response $response, array $args) use ($forumDatabase) {
    $topicID = $args['topicID'];
    $jsonResponse = $forumDatabase->getThreadsByTopic($topicID);
    $response->getBody()->write($jsonResponse);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/forum_backend/api/v3/thread/{threadID}', function (Request $request, Response $response, array $args) use ($forumDatabase) {
    $threadID = $args['threadID'];
    $jsonResponse = $forumDatabase->getThreadWithPostsAndUser($threadID);
    $response->getBody()->write($jsonResponse);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->run();