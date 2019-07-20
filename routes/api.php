<?php 
use Marcohern\Dimages\Lib\DimageConstants;

Route::group([
    'namespace' => 'Marcohern\Dimages\Http\Controllers',
    'prefix' => DimageConstants::DIMROUTE,
    'middleware' => ['api']
], function () {
    Route::get ('/_meta/_index/{entity}/{identity}/{index?}', 'DimageMetaController@index');
    Route::get ('/_meta/_sources/{entity}/{identity}'       , 'DimageMetaController@sources');
    Route::post('/_meta/_switch_index/{entity}/{identity}'  , 'DimageMetaController@switch_index');
    //Route::post('/_meta/stage/{session}'                   , 'DimageMetaController@stage'  );
    //Route::post('/_meta/confirm/{session}'                 , 'DimageMetaController@confirm');

    Route::get('/_meta/{entity}/{identity}/{profile}/{density}/{index?}', 'DimageMetaController@view_exact');
    Route::get('/_meta/{entity}/{identity}/{index?}'                    , 'DimageMetaController@view');

    Route::post  ('/_meta/{entity}/{identity}', "DimageMetaController@store");
    Route::delete('/_meta/{entity}/{identity}', 'DimageMetaController@destroy');

    Route::get('/_meta/{entity}', 'DimageMetaController@identities');
    Route::get('/_meta'         , 'DimageMetaController@entities');

    Route::get   ('_status'                                           , 'DimController@status' );
    Route::post  ('{entity}/{identity}/switch/{source}/with/{target}' , 'DimController@switch' );
    Route::post  ('{entity}/{identity}/normalize'                     , 'DimController@normalize');
    Route::get   ('{entity}/{identity}/{profile}/{density}/{index?}'  , 'DimController@derive' );
    Route::put   ('{entity}/{identity}/{index}'                       , 'DimController@update' );
    Route::delete('{entity}/{identity}/{index?}'                      , 'DimController@destroy');
    Route::get   ('{entity}/{identity}/{index?}'                      , 'DimController@source' );
    Route::post  ('{entity}/{identity}'                               , 'DimController@store'  );
});