<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

/* Silex setup */

$paramFilepath = realpath(__DIR__ . '/../app/parameters.yml');
if (!file_exists($paramFilepath)) {
    throw new \RuntimeException('No parameters.yml');
}

$parameters = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($paramFilepath));

$configFilepath = realpath(__DIR__ . '/../app/config.yml');
$configAsAString = file_get_contents($configFilepath);

$configAsAString = \TODOListApi\Application\ParametersConfigHandler::replaceParametersInConfig(
    $parameters['parameters'],
    $configAsAString
);

$configuration = \Symfony\Component\Yaml\Yaml::parse($configAsAString);
$configuration['cache_path'] = realpath(__DIR__ . '/../app/cache/');
$configuration['doctrine_orm_path'] = realpath(__DIR__ . '/../resources/config/doctrine/');

\TODOListApi\Application\Configurator::configureApp($app, $configuration);

if (isset($parameters['parameters']['environment']) && ('dev' === $parameters['parameters']['environment'])) {
    $app['debug'] = true;
}

/* Routing & controllers */

$app['todolist.controller'] = function ($app) {
    return new \TODOListApi\Controller\TodolistController(
        $app['orm.em'],
        $app['app.todolist.manager'],
        $app['app.todolist.serializer']
    );
};

$app->post('/item', 'todolist.controller:createAction')->bind('api_todolist_create');
$app->get('/items', 'todolist.controller:getAllAction')->bind('api_todolist_get_all');
$app->get('/item/{id}', 'todolist.controller:getAction')->bind('api_todolist_get');
$app->put('/item/{id}/start-progress', 'todolist.controller:startProgressAction')->bind('api_todolist_start_progress');
$app->put('/item/{id}/complete', 'todolist.controller:completeAction')->bind('api_todolist_complete');
$app->delete('/item/{id}', 'todolist.controller:deleteAction')->bind('api_todolist_delete');

$app->run();
