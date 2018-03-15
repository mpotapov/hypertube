<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('movies', 'MoviesController@index');

Route::get('api/get_movies', 'MoviesController@getMovies');

Route::get('parse_movies', 'MoviesController@parseMovies');