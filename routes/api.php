<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'mh/dim/api',
    'middleware' => ['api']
], function () {
    Route::get('/', "DimagesController@api");

    Route::post('/meta/{entity}/{identity}', "DimageMetaController@store");
});