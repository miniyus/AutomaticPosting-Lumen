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
    $res = $router->app->version();
    return $res;
});

$router->group(['prefix' => 'api', 'middleware' => 'client'], function () use ($router) {

    $router->post('stocks/corpCodes', 'StockController@storeCorpCodes');

    $router->get('stocks/sectors/{market}', 'StockController@getSectors');

    $router->get('stocks', 'StockController@index');

    $router->post('stocks', 'StockController@store');
});
