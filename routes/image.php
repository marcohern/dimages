<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'dim'
], function () {
    Route::get('/', "DiController@index");
});