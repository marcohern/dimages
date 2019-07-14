<?php 

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => 'mh/dim/api',
    'middleware' => ['api']
], function () {
    Route::get ('/meta/index/{entity}/{identity}/{index?}', 'DimageMetaController@index'  );
    //Route::post('/meta/stage/{session}'                   , 'DimageMetaController@stage'  );
    //Route::post('/meta/confirm/{session}'                 , 'DimageMetaController@confirm');

    Route::get('/meta/{entity}/{identity}/{profile}/{density}/{index?}', 'DimageMetaController@view_exact');
    Route::get('/meta/{entity}/{identity}/{index?}'                    , 'DimageMetaController@view');

    Route::post('/meta/{entity}/{identity}', "DimageMetaController@store");
    Route::delete('/meta/{entity}/{identity}' , 'DimageMetaController@destroy');

    Route::get('/meta/{entity}'            , 'DimageMetaController@identities');
    Route::get('/meta'                     , 'DimageMetaController@entities');

    Route::get('/status' , 'DimController@status');
    Route::post('/upload/{entity}/{identity}' , 'DimController@store');
    Route::get('{entity}/{identity}/{profile}/{density}/{index?}', "DimController@full");
    Route::get('{entity}/{identity}/{index?}'                    , "DimController@original");
});