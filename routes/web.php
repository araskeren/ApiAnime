<?php

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
    return 'Anime Api With '.$router->app->version();
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {
  $router->get('anime', 'AnimeController@index');
  $router->get('anime/delete', 'AnimeController@indexDelete');
  $router->get('anime/{id}', 'AnimeController@show');
  $router->get('anime/delete/{id}', 'AnimeController@showDelete');
  $router->post('anime', 'AnimeController@store');
  $router->post('anime/delete/{id}', 'AnimeController@restore');
  $router->post('anime/{id}', 'AnimeController@update');
  $router->delete('anime/{id}', 'AnimeController@destroy');
  $router->delete('anime/delete/{id}', 'AnimeController@harddestroy');
});
