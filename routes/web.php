<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'dimages',
    'middleware' => ['web']
], function () {
    Route::get('/', "DimagesController@index");
});