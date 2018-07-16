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
  $router->get('anime/{id}/cover','AnimeController@viewImage');
  $router->get('anime/{id}/restore', 'AnimeController@restore');
  $router->post('anime', 'AnimeController@store');
  $router->post('anime/{id}', 'AnimeController@update');
  $router->delete('anime/{id}', 'AnimeController@destroy');
  $router->delete('anime/{id}/destroy', 'AnimeController@harddestroy');

  $router->get('anime/{id}/episode/', 'EpisodeController@index');
  $router->get('anime/{id}/episode/delete', 'EpisodeController@indexDelete');
  $router->get('anime/{id}/episode/{ep}', 'EpisodeController@show');
  $router->get('anime/{id}/episode/{ep}/delete', 'EpisodeController@showDelete');
  $router->get('anime/{id}/episode/{ep}/cover','EpisodeController@viewImage');
  $router->get('anime/{id}/episode/{ep}/restore', 'EpisodeController@restore');
  $router->post('anime/{id}/episode/', 'EpisodeController@store');
  $router->post('anime/{id}/episode/{ep}', 'EpisodeController@update');
  $router->delete('anime/{id}/episode/{ep}', 'EpisodeController@destroy');
  $router->delete('anime/{id}/episode/{ep}/destroy', 'EpisodeController@harddestroy');

  $router->get('genre/', 'GenreController@index');
  $router->get('genre/delete', 'GenreController@indexDelete');
  $router->get('genre/{id}', 'GenreController@show');
  $router->get('genre/{id}/delete', 'GenreController@showDelete');
  $router->get('genre/{id}/restore', 'GenreController@restore');
  $router->post('genre/', 'GenreController@store');
  $router->post('genre/{id}', 'GenreController@update');
  $router->delete('genre/{id}', 'GenreController@destroy');
  $router->delete('genre/{id}/destroy', 'GenreController@harddestroy');

  $router->get('licensor/', 'LicensorController@index');
  $router->get('licensor/delete', 'LicensorController@indexDelete');
  $router->get('licensor/{id}', 'LicensorController@show');
  $router->get('licensor/{id}/delete', 'LicensorController@showDelete');
  $router->get('licensor/{id}/restore', 'LicensorController@restore');
  $router->post('licensor/', 'LicensorController@store');
  $router->post('licensor/{id}', 'LicensorController@update');
  $router->delete('licensor/{id}', 'LicensorController@destroy');
  $router->delete('licensor/{id}/destroy', 'LicensorController@harddestroy');

  $router->get('produser/', 'ProduserController@index');
  $router->get('produser/delete', 'ProduserController@indexDelete');
  $router->get('produser/{id}', 'ProduserController@show');
  $router->get('produser/{id}/delete', 'ProduserController@showDelete');
  $router->get('produser/{id}/restore', 'ProduserController@restore');
  $router->post('produser/', 'ProduserController@store');
  $router->post('produser/{id}', 'ProduserController@update');
  $router->delete('produser/{id}', 'ProduserController@destroy');
  $router->delete('produser/{id}/destroy', 'ProduserController@harddestroy');

  $router->get('history/', 'HistoryController@index');
  $router->get('history/delete', 'HistoryController@indexDelete');
  $router->get('history/{id}/restore', 'HistoryController@restore');
  $router->post('history/', 'HistoryController@store');
  $router->delete('history/{id}', 'HistoryController@destroy');
  $router->delete('history/{id}/destroy', 'HistoryController@harddestroy');

  $router->get('komentar/', 'ProduserController@index');
  $router->get('produser/delete', 'ProduserController@indexDelete');
  $router->get('produser/{id}', 'ProduserController@show');
  $router->get('produser/{id}/delete', 'ProduserController@showDelete');
  $router->get('produser/{id}/restore', 'ProduserController@restore');
  $router->post('produser/', 'ProduserController@store');
  $router->post('produser/{id}', 'ProduserController@update');
  $router->delete('produser/{id}', 'ProduserController@destroy');
  $router->delete('produser/{id}/destroy', 'ProduserController@harddestroy');

});
