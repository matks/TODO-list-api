<?php

$app = require_once __DIR__.'/../app/bootstrap.php';

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
/* Warning: Symfony needs header "Content-Type : application/x-www-form-urlencoded" for PUT data */
$app->put('/item/{id}', 'todolist.controller:updateAction')->bind('api_todolist_update');
$app->get('/reporters', 'todolist.controller:getReportersAction')->bind('api_todolist_get_reporters');
$app->get('/item/{id}', 'todolist.controller:getAction')->bind('api_todolist_get');
$app->post('/item/{id}/start-progress', 'todolist.controller:startProgressAction')->bind('api_todolist_start_progress');
$app->post('/item/{id}/complete', 'todolist.controller:completeAction')->bind('api_todolist_complete');
$app->post('/item/{id}/reset-status', 'todolist.controller:resetStatusAction')->bind('api_todolist_reset_status');
$app->delete('/item/{id}', 'todolist.controller:deleteAction')->bind('api_todolist_delete');

$app->run();
