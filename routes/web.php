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

$router->post('register','UserController@store');
$router->group(['prefix' => 'api/v1'], function () use ($router) {

  $router->get('anime', 'AnimeController@index');
  $router->get('anime/delete', 'AnimeController@indexDelete');
  $router->get('anime/{slug}', 'AnimeController@show');
  //$router->get('anime/delete/{slug}', 'AnimeController@showDelete');
  $router->get('anime/{slug}/cover','AnimeController@viewImage');
  $router->get('anime/{slug}/restore', 'AnimeController@restore');
  $router->post('anime', 'AnimeController@store');
  $router->post('anime/{slug}', 'AnimeController@update');
  $router->delete('anime/{slug}', 'AnimeController@destroy');
  $router->delete('anime/{slug}/destroy', 'AnimeController@harddestroy');

  //$router->get('anime/{slug}', 'AnimeController@index');
  $router->get('anime/{slug_anime}/season/delete', 'SeasonController@indexDelete');
  $router->get('anime/{slug_anime}/{slug_season}', 'SeasonController@show');
  //$router->get('anime/delete/{slug}', 'AnimeController@showDelete');
  $router->get('anime/{slug_anime}/{slug_season}/cover','SeasonController@viewImage');
  $router->get('anime/{slug_anime}/{slug_season}/restore', 'SeasonController@restore');
  $router->post('anime/{slug_anime}/season', 'SeasonController@store');
  $router->post('anime/{slug_anime}/{slug_season}', 'SeasonController@update');
  $router->delete('anime/{slug_anime}/{slug_season}', 'SeasonController@destroy');
  $router->delete('anime/{slug_anime}/{slug_season}/destroy', 'SeasonController@harddestroy');

  $router->get('anime/{slug_anime}/{slug_season}/episode/', 'EpisodeController@index');
  $router->get('anime/{slug_anime}/{slug_season}/delete', 'EpisodeController@indexDelete');
  $router->get('anime/{slug_anime}/{slug_season}/{ep}', 'EpisodeController@show');
  //$router->get('anime/{slug_anime}/{slug_season}/{ep}/delete', 'EpisodeController@showDelete');
  $router->get('anime/{slug_anime}/{slug_season}/{ep}/cover','EpisodeController@viewImage');
  $router->get('anime/{slug_anime}/{slug_season}/{ep}/restore', 'EpisodeController@restore');
  $router->post('anime/{slug_anime}/{slug_season}/episode/', 'EpisodeController@store');
  $router->post('anime/{slug_anime}/{slug_season}/{ep}', 'EpisodeController@update');
  $router->delete('anime/{slug_anime}/{slug_season}/{ep}', 'EpisodeController@destroy');
  $router->delete('anime/{slug_anime}/{slug_season}/{ep}/destroy', 'EpisodeController@harddestroy');

  $router->get('anime/{slug_anime}/{slug_season}/{ep}/server', 'ServerController@index');
  $router->get('anime/{slug_anime}/{slug_season}/{ep}/delete', 'ServerController@indexDelete');
  $router->get('anime/{slug_anime}/{slug_season}/{ep}/{server}/restore', 'ServerController@restore');
  $router->post('anime/{slug_anime}/{slug_season}/{ep}/server', 'ServerController@store');
  $router->post('anime/{slug_anime}/{slug_season}/{ep}/{server}', 'ServerController@update');
  $router->delete('anime/{slug_anime}/{slug_season}/{ep}/{server}', 'ServerController@destroy');
  $router->delete('anime/{slug_anime}/{slug_season}/{ep}/{server}/destroy', 'ServerController@harddestroy');

  $router->get('genre/', 'GenreController@index');
  $router->get('genre/delete', 'GenreController@indexDelete');
  $router->get('genre/{slug}', 'GenreController@show');
  $router->get('genre/{slug}/delete', 'GenreController@showDelete');
  $router->get('genre/{slug}/restore', 'GenreController@restore');
  $router->post('genre/', 'GenreController@store');
  $router->post('genre/{slug}', 'GenreController@update');
  $router->delete('genre/{slug}', 'GenreController@destroy');
  $router->delete('genre/{slug}/destroy', 'GenreController@harddestroy');

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

  $router->get('studio/', 'StudioController@index');
  $router->get('studio/delete', 'StudioController@indexDelete');
  $router->get('studio/{slug}', 'StudioController@show');
  $router->get('studio/{slug}/restore', 'StudioController@restore');
  $router->post('studio/', 'StudioController@store');
  $router->post('studio/{slug}', 'StudioController@update');
  $router->delete('studio/{slug}', 'StudioController@destroy');
  $router->delete('studio/{slug}/destroy', 'StudioController@harddestroy');

  $router->get('history/', 'HistoryController@index');
  $router->get('history/delete', 'HistoryController@indexDelete');
  $router->get('history/{id}/restore', 'HistoryController@restore');
  $router->post('history/', 'HistoryController@store');
  $router->delete('history/{id}', 'HistoryController@destroy');
  $router->delete('history/{id}/destroy', 'HistoryController@harddestroy');

  $router->get('komentar/', 'KomentarController@index');
  $router->get('komentar/delete', 'KomentarController@indexDelete');
  $router->get('komentar/{id}/restore', 'KomentarController@restore');
  $router->post('komentar/', 'KomentarController@store');
  $router->post('komentar/{id}', 'KomentarController@update');
  $router->delete('komentar/{id}', 'KomentarController@destroy');
  $router->delete('komentar/{id}/destroy', 'KomentarController@harddestroy');

  $router->get('subscribe/', 'SubscribeController@index');
  $router->post('subscribe/', 'SubscribeController@store');
  $router->delete('subscribe/{id}/destroy', 'SubscribeController@harddestroy');

  $router->get('playlist/', 'PlaylistController@index');
  $router->post('playlist/', 'PlaylistController@store');
  $router->delete('playlist/{id}/destroy', 'PlaylistController@harddestroy');

});
