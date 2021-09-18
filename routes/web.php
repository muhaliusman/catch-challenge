<?php

use Illuminate\Support\Facades\Route;

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

Route::redirect('/', 'item-order');

Route::group(['prefix' => 'item-order', 'as' => 'item-order.'], function ($route) {
    $route->get('/', 'ItemOrderController@index')->name('index');
    $route->post('generate-csv', 'ItemOrderController@generateCsv')->name('generate-csv');
    $route->get('{id}/download-csv', 'ItemOrderController@downloadCsv')->name('download-csv');
});
