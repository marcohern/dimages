<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'mhn/dim/api',
    'middleware' => ['api']
], function () {
    Route::get('/', "DimagesController@api");
});