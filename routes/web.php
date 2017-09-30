<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/show-table', [
    'as' => 'table',
    'uses' => 'DashboardController@showTable',
]);

Route::get('about', [
    'as' => 'about',
    'uses' => 'DashboardController@about',
]);

Route::get('load', [
    'as' => 'load-info',
    'uses' => 'DashboardController@loadInfo',
]);

Route::get('/{group?}', [
    'as' => 'dashboard',
    'uses' => 'DashboardController@index',
]);