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
    $topics = $forumDatabase->getTopics();
    $jsonResponse = json_encode($topics, JSON_PRETTY_PRINT);
    $response->getBody()->write($jsonResponse);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/forum_backend/api/v3/thread/{threadID}', function (Request $request, Response $response, array $args) use ($forumDatabase) {
    $threadID = $args['threadID'];
    $threads = $forumDatabase->getThreadWithPostsAndUser($threadID);
    $jsonResponse = json_encode($threads, JSON_PRETTY_PRINT);
    $response->getBody()->write($jsonResponse);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->get('/forum_backend/api/v3/topics/{topicID}/threads', function (Request $request, Response $response, array $args) use ($forumDatabase) {
    $topicID = $args['topicID'];
    $threads = $forumDatabase->getThreadsByTopic($topicID);
    $jsonResponse = json_encode($threads, JSON_PRETTY_PRINT);
    $response->getBody()->write($jsonResponse);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});


$app->get('/forum_backend/api/v3/thread/{threadID}/post', function (Request $request, Response $response, array $args) use ($forumDatabase) {
    $threadID = $args['threadID'];
    $posts = $forumDatabase->getPostsByThread($threadID);
    $dataContainer = array("data" => $posts);
    $jsonResponse = json_encode($dataContainer, JSON_PRETTY_PRINT);
    $response->getBody()->write($jsonResponse);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->run();