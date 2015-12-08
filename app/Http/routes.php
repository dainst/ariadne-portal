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

Route::get('/', 'PageController@welcome');
Route::get('about', 'PageController@about');
Route::get('robots.txt','PageController@robots' );

Route::get('provider', 'ProviderController@index');

Route::get('subject', 'SubjectController@index');

Route::get('browse/map', 'BrowseController@map');
Route::get('browse/when', 'BrowseController@when');

Route::get('search', [
    'as'=> 'search',
    'uses' => 'ResourceController@search'
]);

/* resource routes, implementing "cool URIs" (http://www.w3.org/TR/cooluris/) */

Route::get('page/{id}', [
    'as' => 'resource.page',
    'uses' => 'ResourceController@page'
]);
Route::get('data/{id}.json', 'ResourceController@data');
Route::get('data/{id}', [
    'as' => 'resource.data',
    'uses' => 'ResourceController@data'
]);
Route::get('resource/{id}.json', 'ResourceController@data');
Route::get('resource/{id}', [
    'as' => 'resourece',
    'uses' => 'ResourceController@negotiate'
]);