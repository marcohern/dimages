<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'mh/dim/api',
    'middleware' => ['api']
], function () {
    Route::get('/', "DimagesController@api");

    Route::get('/meta/index/{entity}/{identity}/{index?}', 'DimageMetaController@index');
    Route::get('/meta/{entity}/{identity}/{profile}/{densoty}/{index?}', 'DimageMetaController@view_exact');
    Route::get('/meta/{entity}/{identity}/{index?}', 'DimageMetaController@view');

    Route::post('/meta/{entity}/{identity}', "DimageMetaController@store");

    Route::get('/meta/{entity}', 'DimageMetaController@identities');
    Route::get('/meta', 'DimageMetaController@entities');
});