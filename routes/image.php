<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'mhn/dim'
], function () {
    Route::get('/', "DiController@index");
});