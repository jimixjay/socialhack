<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'match'], function() use ($router) {
    $router->post('create', 'Match\CreateController@execute');
    $router->post('delete', 'Match\DeleteController@execute');
});

$router->group(['prefix' => 'user'], function() use ($router) {
    $router->post('create', 'User\CreateController@execute');
    $router->post('delete', 'User\DeleteController@execute');
});

$router->group(['prefix' => 'partner'], function() use ($router) {
    $router->get('list', 'Partner\ListController@execute');
});